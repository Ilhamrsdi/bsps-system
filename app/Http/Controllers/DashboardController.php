<?php

namespace App\Http\Controllers;

use App\Models\DataPenerima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            if (Auth::user()->isPetugas()) {
                return redirect()->route('petugas.dashboard');
            }
            if (Auth::user()->isAdminKecamatan()) {
                return redirect()->route('dashboard.kecamatan');
            }
        }

        // 1. Ringkasan Utama dari Database Real (DataPenerima)
        $totalPenerima = DataPenerima::distinct('no_ktp')->count('no_ktp');
        $totalKecamatan = DataPenerima::distinct('kecamatan')->count('kecamatan');
        $totalDesa = DataPenerima::selectRaw("COUNT(DISTINCT CONCAT(kecamatan, ' - ', desa_kelurahan)) as total")->value('total');

        // 2. Statistik Desil Global (Backlog 1 vs Backlog 2 vs Usulan Baru Petugas)
        $desilStats = DataPenerima::selectRaw('pengelompokan_desil, count(*) as total')
            ->groupBy('pengelompokan_desil')
            ->pluck('total', 'pengelompokan_desil');

        $backlog1Count   = $desilStats['Backlog 1 Desil 1-4'] ?? 0;
        $backlog2Count   = $desilStats['Backlog 2 Desil 1-4'] ?? 0;
        $usulanBaruCount = DataPenerima::where(function ($q) {
            $q->where('pengelompokan_desil', 'like', '%Usulan%')
              ->orWhere('status', 'Usulan Petugas');
        })->count();

        // 3. Top Kecamatan dengan Usulan BSPS Terbanyak & Breakdown Desil per Kecamatan
        $rawKecamatan = DataPenerima::selectRaw('kecamatan, 
            count(*) as total,
            SUM(CASE WHEN pengelompokan_desil LIKE "%Backlog 1%" THEN 1 ELSE 0 END) as backlog_1,
            SUM(CASE WHEN pengelompokan_desil LIKE "%Backlog 2%" THEN 1 ELSE 0 END) as backlog_2')
            ->groupBy('kecamatan')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $topKecamatan = $rawKecamatan;

        $sudahSql = DataPenerima::getSudahSql();

        // 4. Top 6 Desa/Kelurahan dengan Capaian Layak Terbanyak
        $topDesa = DataPenerima::selectRaw("
            desa_kelurahan,
            kecamatan,
            COUNT(*) as total,
            SUM(CASE WHEN {$sudahSql} THEN 1 ELSE 0 END) as sudah_survei,
            SUM(CASE WHEN status_kelayakan = 'Layak Diusulkan' THEN 1 ELSE 0 END) as total_layak
        ")
        ->whereNotNull('desa_kelurahan')
        ->where('desa_kelurahan', '!=', '')
        ->groupBy('desa_kelurahan', 'kecamatan')
        ->orderByDesc('total_layak')
        ->orderByDesc('sudah_survei')
        ->limit(6)
        ->get();

        // 5. Statistik Jenis Kelamin Kepala Keluarga
        $genderStats = DataPenerima::selectRaw('jenis_kelamin, count(*) as total')
            ->groupBy('jenis_kelamin')
            ->pluck('total', 'jenis_kelamin');

        $lakiCount = $genderStats['L'] ?? 0;
        $perempuanCount = $genderStats['P'] ?? 0;

        // 6. Sampel Calon Penerima Terbaru
        $latestCandidates = DataPenerima::limit(6)->get();

        // 7. Data untuk Stacked Bar Chart.js (Top 8 Kecamatan dengan Breakdown Desil)
        $chartTop8 = $topKecamatan->take(8);
        $chartKecamatanLabels = $chartTop8->pluck('kecamatan')->map(function($kec) {
            return ucwords(strtolower($kec));
        });
        $chartBacklog1Data = $chartTop8->pluck('backlog_1');
        $chartBacklog2Data = $chartTop8->pluck('backlog_2');
        $chartTotalData = $chartTop8->pluck('total');

        // 8. STATISTIK GLOBAL VERVAL & CAPAIAN PER DESA SE-KABUPATEN JEMBER (~12.000 DATA)
        $sudahSql = DataPenerima::getSudahSql();

        $globalTotalTarget = $totalPenerima;
        $globalTotalSudah = DataPenerima::sudahSurvei()->count();
        $globalTotalBelum = max(0, $globalTotalTarget - $globalTotalSudah);
        $globalTotalLayak = DataPenerima::where('status_kelayakan', 'Layak Diusulkan')->count();
        $globalTotalTidakLayak = DataPenerima::where('status_kelayakan', 'Tidak Layak Diusulkan')->count();
        $globalTotalMypkp = DataPenerima::where('is_mypkp', true)->count();
        $globalPersenSurvei = $globalTotalTarget > 0 ? round(($globalTotalSudah / $globalTotalTarget) * 100, 1) : 0;
        $globalPersenLayak = $globalTotalSudah > 0 ? round(($globalTotalLayak / $globalTotalSudah) * 100, 1) : 0;

        $globalVervalStats = [
            'total_target' => $globalTotalTarget,
            'total_sudah' => $globalTotalSudah,
            'total_belum' => $globalTotalBelum,
            'total_layak' => $globalTotalLayak,
            'total_tidak_layak' => $globalTotalTidakLayak,
            'total_mypkp' => $globalTotalMypkp,
            'persen_survei' => $globalPersenSurvei,
            'persen_layak' => $globalPersenLayak,
        ];

        // Statistik per-status verval
        $statusVervalStats = DataPenerima::selectRaw("
            SUM(CASE WHEN status = 'ditemukan' THEN 1 ELSE 0 END) as ditemukan,
            SUM(CASE WHEN status = 'meninggal' THEN 1 ELSE 0 END) as meninggal,
            SUM(CASE WHEN status = 'pindah' THEN 1 ELSE 0 END) as pindah,
            SUM(CASE WHEN status = 'menolak disurvey' THEN 1 ELSE 0 END) as menolak,
            SUM(CASE WHEN status = 'tidak diketahui' THEN 1 ELSE 0 END) as tidak_diketahui,
            SUM(CASE WHEN (status IS NULL OR status = '' OR status NOT IN ('ditemukan','meninggal','pindah','menolak disurvey','tidak diketahui')) THEN 1 ELSE 0 END) as belum_verval
        ")->first();

        // Agregasi Desa & Kecamatan Lengkap (Semua Kecamatan & Seluruh Desa se-Kabupaten Jember)
        $desaStats = DataPenerima::selectRaw("
            UPPER(TRIM(kecamatan)) as kecamatan,
            UPPER(TRIM(desa_kelurahan)) as desa_kelurahan,
            COUNT(*) as total_target,
            SUM(CASE WHEN {$sudahSql} THEN 1 ELSE 0 END) as total_sudah,
            SUM(CASE WHEN status_kelayakan = 'Layak Diusulkan' THEN 1 ELSE 0 END) as total_layak,
            SUM(CASE WHEN status_kelayakan = 'Tidak Layak Diusulkan' THEN 1 ELSE 0 END) as total_tidak_layak
        ")
        ->whereNotNull('kecamatan')
        ->where('kecamatan', '!=', '')
        ->whereNotNull('desa_kelurahan')
        ->where('desa_kelurahan', '!=', '')
        ->groupBy(DB::raw('UPPER(TRIM(kecamatan))'), DB::raw('UPPER(TRIM(desa_kelurahan))'))
        ->orderBy(DB::raw('UPPER(TRIM(kecamatan))'))
        ->orderBy(DB::raw('UPPER(TRIM(desa_kelurahan))'))
        ->get();

        $rekapPerKecamatan = $desaStats->groupBy('kecamatan')->map(function ($desas, $kecName) {
            $totalTarget = $desas->sum('total_target');
            $totalSudah  = $desas->sum('total_sudah');
            $totalLayak  = $desas->sum('total_layak');
            $totalTidak  = $desas->sum('total_tidak_layak');
            $totalBelum  = max(0, $totalTarget - $totalSudah);
            $progresPercent = $totalTarget > 0 ? round(($totalSudah / $totalTarget) * 100, 1) : 0;
            $layakPercent   = $totalSudah > 0 ? round(($totalLayak / $totalSudah) * 100, 1) : 0;

            // Rincian Kartu Desa
            $desaCards = $desas->map(function ($d) {
                $target = (int)$d->total_target;
                $sudah = (int)$d->total_sudah;
                $layak = (int)$d->total_layak;
                $tidak = (int)$d->total_tidak_layak;
                $belum = max(0, $target - $sudah);
                $progres = $target > 0 ? round(($sudah / $target) * 100, 1) : 0;
                $persenLayak = $sudah > 0 ? round(($layak / $sudah) * 100, 1) : 0;
                $persenTidak = $sudah > 0 ? round(($tidak / $sudah) * 100, 1) : 0;

                return (object)[
                    'desa_kelurahan' => $d->desa_kelurahan,
                    'total_target' => $target,
                    'total_sudah' => $sudah,
                    'total_layak' => $layak,
                    'total_tidak_layak' => $tidak,
                    'total_belum' => $belum,
                    'progres_percent' => $progres,
                    'persen_layak' => $persenLayak,
                    'persen_tidak' => $persenTidak,
                ];
            });

            return (object)[
                'kecamatan' => $kecName,
                'total_desa' => $desas->count(),
                'total_target' => $totalTarget,
                'total_sudah' => $totalSudah,
                'total_layak' => $totalLayak,
                'total_tidak_layak' => $totalTidak,
                'total_belum' => $totalBelum,
                'progres_percent' => $progresPercent,
                'layak_percent' => $layakPercent,
                'desa_list' => $desaCards,
            ];
        });

        // 9. Ranking Kecamatan Berdasarkan Capaian Layak Terbanyak (Seluruh 31 Kecamatan)
        $rankingKecamatan = $rekapPerKecamatan->sortByDesc(function ($item) {
            return ($item->total_layak * 1000000) + $item->total_sudah;
        })->values();

        $top1KecamatanCapaian = $rankingKecamatan->first();
        $allKecamatanCapaian = $rankingKecamatan;

        return view('dashboard.index', compact(
            'totalPenerima',
            'totalKecamatan',
            'totalDesa',
            'topKecamatan',
            'topDesa',
            'backlog1Count',
            'backlog2Count',
            'usulanBaruCount',
            'lakiCount',
            'perempuanCount',
            'latestCandidates',
            'chartKecamatanLabels',
            'chartBacklog1Data',
            'chartBacklog2Data',
            'chartTotalData',
            'globalVervalStats',
            'rekapPerKecamatan',
            'rankingKecamatan',
            'top1KecamatanCapaian',
            'allKecamatanCapaian',
            'statusVervalStats'
        ));
    }

    /**
     * Halaman Rincian Data Penerima Berdasarkan Status Kelayakan (Layak Diusulkan / Tidak Layak)
     */
    public function dataKelayakan(Request $request)
    {
        $status = $request->get('status', 'layak'); // 'layak', 'tidak_layak', 'all'
        $kecamatan = $request->get('kecamatan');
        $desa = $request->get('desa');
        $search = $request->get('search');
        $perPage = $request->get('per_page', '15');
        $perPageLimit = ($perPage === 'all') ? 10000 : (int)$perPage;
        $statusVerval = $request->get('status_verval'); // filter status lapangan

        $user = Auth::user();
        if ($user && $user->isAdminKecamatan()) {
            $kecamatan = $user->kecamatan;
        }

        // Summary Counts Global
        $totalLayakGlobal = DataPenerima::where('status_kelayakan', 'Layak Diusulkan')->count();
        $totalTidakLayakGlobal = DataPenerima::where('status_kelayakan', 'Tidak Layak Diusulkan')->count();
        $totalMypkpGlobal = DataPenerima::where('is_mypkp', true)->count();
        $totalSudahSurveiGlobal = DataPenerima::sudahSurvei()->count();
        $totalTargetGlobal = DataPenerima::count();

        // Base Query
        $query = DataPenerima::with('petugas');

        if ($status === 'layak') {
            $query->where('status_kelayakan', 'Layak Diusulkan');
        } elseif ($status === 'tidak_layak') {
            $query->where('status_kelayakan', 'Tidak Layak Diusulkan');
        } elseif ($status === 'mypkp') {
            $query->where('is_mypkp', true);
        } else {
            $query->sudahSurvei();
        }

        if ($kecamatan && $kecamatan !== 'all') {
            $query->whereRaw('LOWER(TRIM(kecamatan)) = ?', [strtolower(trim($kecamatan))]);
        }
        if ($desa && $desa !== 'all') {
            $query->whereRaw('LOWER(TRIM(desa_kelurahan)) = ?', [strtolower(trim($desa))]);
        }
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_ktp', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('desa_kelurahan', 'like', "%{$search}%")
                  ->orWhere('kecamatan', 'like', "%{$search}%");
            });
        }
        
        $statusFoto = $request->get('status_foto');
        if ($statusFoto === 'ditolak') {
            $query->where(function ($q) {
                $q->where('status_foto_sudut_depan', 'tidak layak')
                  ->orWhere('status_foto_sudut_belakang', 'tidak layak')
                  ->orWhere('status_foto_bagian_dalam', 'tidak layak')
                  ->orWhere('status_foto_sudut_kiri', 'tidak layak')
                  ->orWhere('status_foto_sudut_kanan', 'tidak layak')
                  ->orWhere('status_ktp', 'tidak layak')
                  ->orWhere('status_kk', 'tidak layak')
                  ->orWhere('status_surat_pernyataan', 'tidak layak');
            });
        }

        // Filter status verval (ditemukan, meninggal, pindah, dll.)
        $validStatusVerval = ['ditemukan', 'meninggal', 'pindah', 'menolak disurvey', 'tidak diketahui', 'belum_verval'];
        if ($statusVerval && in_array($statusVerval, $validStatusVerval)) {
            if ($statusVerval === 'belum_verval') {
                $query->where(function ($q) {
                    $q->whereNull('status')
                      ->orWhere('status', '')
                      ->orWhereNotIn('status', ['ditemukan', 'meninggal', 'pindah', 'menolak disurvey', 'tidak diketahui']);
                });
            } else {
                $query->where('status', $statusVerval);
            }
        }

        if ($status === 'layak' || $status === 'mypkp') {
            $query->select('*')
                  ->selectRaw('(IF(indikator_lantai = "tidak_ada", 1, 0) + IF(indikator_pondasi = "tidak_ada", 1, 0) + IF(indikator_dinding = "tidak_ada", 1, 0) + IF(indikator_struktur = "tidak_ada", 1, 0) + IF(indikator_atap = "tidak_ada", 1, 0) + IF(indikator_penghasilan = "ada", 1, 0)) as jumlah_indikator_rusak')
                  ->orderByDesc('jumlah_indikator_rusak');
        }

        $penerimaList = $query->orderBy('nama', 'asc')->paginate($perPageLimit)->withQueryString();

        // List Kecamatan & Desa untuk dropdown filter
        $listKecamatan = DataPenerima::whereNotNull('kecamatan')
            ->where('kecamatan', '!=', '')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');

        $desaQuery = DataPenerima::whereNotNull('desa_kelurahan')
            ->where('desa_kelurahan', '!=', '')
            ->distinct();
        if ($kecamatan && $kecamatan !== 'all') {
            $desaQuery->whereRaw('LOWER(TRIM(kecamatan)) = ?', [strtolower(trim($kecamatan))]);
        }
        $listDesa = $desaQuery->orderBy('desa_kelurahan')->pluck('desa_kelurahan');

        return view('dashboard.data_kelayakan', compact(
            'status',
            'penerimaList',
            'totalLayakGlobal',
            'totalTidakLayakGlobal',
            'totalMypkpGlobal',
            'totalSudahSurveiGlobal',
            'totalTargetGlobal',
            'listKecamatan',
            'listDesa',
            'kecamatan',
            'desa',
            'search',
            'perPage',
            'statusFoto',
            'statusVerval'
        ));
    }

    /**
     * Update Status Kelayakan & Indikator
     */
    public function updateStatusKelayakan(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'indikator_atap' => 'required|in:ada,tidak_ada',
            'indikator_dinding' => 'required|in:ada,tidak_ada',
            'indikator_lantai' => 'required|in:ada,tidak_ada',
            'indikator_pondasi' => 'required|in:ada,tidak_ada',
            'indikator_struktur' => 'required|in:ada,tidak_ada',
            'indikator_penghasilan' => 'required|in:ada,tidak_ada',
            'status_foto_sudut_depan' => 'nullable|in:layak,tidak layak',
            'status_foto_sudut_belakang' => 'nullable|in:layak,tidak layak',
            'status_foto_bagian_dalam' => 'nullable|in:layak,tidak layak',
            'status_foto_sudut_kiri' => 'nullable|in:layak,tidak layak',
            'status_foto_sudut_kanan' => 'nullable|in:layak,tidak layak',
            'catatan_foto_sudut_depan' => 'nullable|string',
            'catatan_foto_sudut_belakang' => 'nullable|string',
            'catatan_foto_bagian_dalam' => 'nullable|string',
            'catatan_foto_sudut_kiri' => 'nullable|string',
            'catatan_foto_sudut_kanan' => 'nullable|string',
            'status_ktp' => 'nullable|in:layak,tidak layak',
            'catatan_ktp' => 'nullable|string',
            'status_kk' => 'nullable|in:layak,tidak layak',
            'catatan_kk' => 'nullable|string',
            'status_surat_pernyataan' => 'nullable|in:layak,tidak layak',
            'catatan_surat_pernyataan' => 'nullable|string',
        ]);

        $penerima = DataPenerima::findOrFail($id);

        $totalIndikatorRtlh = 0;
        if ($request->indikator_lantai === 'tidak_ada') $totalIndikatorRtlh++;
        if ($request->indikator_pondasi === 'tidak_ada') $totalIndikatorRtlh++;
        if ($request->indikator_dinding === 'tidak_ada') $totalIndikatorRtlh++;
        if ($request->indikator_struktur === 'tidak_ada') $totalIndikatorRtlh++;
        if ($request->indikator_atap === 'tidak_ada') $totalIndikatorRtlh++;
        if ($request->indikator_penghasilan === 'ada') $totalIndikatorRtlh++;

        $status_kelayakan = $totalIndikatorRtlh >= 2 ? 'Layak Diusulkan' : 'Tidak Layak Diusulkan';

        $penerima->update([
            'indikator_atap' => $request->indikator_atap,
            'indikator_dinding' => $request->indikator_dinding,
            'indikator_lantai' => $request->indikator_lantai,
            'indikator_pondasi' => $request->indikator_pondasi,
            'indikator_struktur' => $request->indikator_struktur,
            'indikator_penghasilan' => $request->indikator_penghasilan,
            'status_kelayakan' => $status_kelayakan,
            'status_foto_sudut_depan' => $request->status_foto_sudut_depan,
            'status_foto_sudut_belakang' => $request->status_foto_sudut_belakang,
            'status_foto_bagian_dalam' => $request->status_foto_bagian_dalam,
            'status_foto_sudut_kiri' => $request->status_foto_sudut_kiri,
            'status_foto_sudut_kanan' => $request->status_foto_sudut_kanan,
            'catatan_foto_sudut_depan' => $request->catatan_foto_sudut_depan,
            'catatan_foto_sudut_belakang' => $request->catatan_foto_sudut_belakang,
            'catatan_foto_bagian_dalam' => $request->catatan_foto_bagian_dalam,
            'catatan_foto_sudut_kiri' => $request->catatan_foto_sudut_kiri,
            'catatan_foto_sudut_kanan' => $request->catatan_foto_sudut_kanan,
            'status_ktp' => $request->status_ktp,
            'catatan_ktp' => $request->catatan_ktp,
            'status_kk' => $request->status_kk,
            'catatan_kk' => $request->catatan_kk,
            'status_surat_pernyataan' => $request->status_surat_pernyataan,
            'catatan_surat_pernyataan' => $request->catatan_surat_pernyataan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Indikator dan status kelayakan berhasil diperbarui'
        ]);
    }

    /**
     * Halaman Monitoring Progress & Kelayakan Khusus Tingkat Desa / Kelurahan
     */
    public function desa(Request $request)
    {
        $user = Auth::user();

        // 1. Tentukan Kecamatan
        $kecamatanParam = $request->get('kecamatan');
        if ($user && $user->isAdminKecamatan() && $user->kecamatan) {
            $kecamatanParam = $user->kecamatan;
        }

        if (!$kecamatanParam || $kecamatanParam === 'all') {
            $kecamatanParam = DataPenerima::whereNotNull('kecamatan')
                ->where('kecamatan', '!=', '')
                ->orderBy('kecamatan', 'asc')
                ->value('kecamatan') ?: 'AJUNG';
        }

        $kecamatanLower = strtolower(trim($kecamatanParam));

        // 2. Tentukan Desa
        $desaParam = $request->get('desa');
        if (!$desaParam || $desaParam === 'all') {
            $desaParam = DataPenerima::whereRaw('LOWER(TRIM(kecamatan)) = ?', [$kecamatanLower])
                ->whereNotNull('desa_kelurahan')
                ->where('desa_kelurahan', '!=', '')
                ->orderBy('desa_kelurahan', 'asc')
                ->value('desa_kelurahan') ?: '';
        }

        $desaLower = strtolower(trim($desaParam));

        // Query Dasar Desa
        $baseQuery = DataPenerima::whereRaw('LOWER(TRIM(kecamatan)) = ?', [$kecamatanLower])
            ->whereRaw('LOWER(TRIM(desa_kelurahan)) = ?', [$desaLower]);

        // Hitung Metrik Eksekutif Tingkat Desa
        $totalTarget = (clone $baseQuery)->count();
        $totalSudah = (clone $baseQuery)->sudahSurvei()->count();
        $totalBelum = max(0, $totalTarget - $totalSudah);
        $totalLayak = (clone $baseQuery)->where('status_kelayakan', 'Layak Diusulkan')->count();
        $totalTidakLayak = (clone $baseQuery)->where('status_kelayakan', 'Tidak Layak Diusulkan')->count();
        $totalMypkp = (clone $baseQuery)->where('is_mypkp', true)->count();

        $progresPercent = $totalTarget > 0 ? round(($totalSudah / $totalTarget) * 100, 1) : 0;
        $persenLayak = $totalSudah > 0 ? round(($totalLayak / $totalSudah) * 100, 1) : 0;
        $persenTidak = $totalSudah > 0 ? round(($totalTidakLayak / $totalSudah) * 100, 1) : 0;

        $backlog1 = (clone $baseQuery)->where('pengelompokan_desil', 'like', '%Backlog 1%')->count();
        $backlog2 = (clone $baseQuery)->where('pengelompokan_desil', 'like', '%Backlog 2%')->count();

        // Petugas Lapangan Desa
        $petugasList = \App\Models\User::whereRaw('LOWER(TRIM(kecamatan)) = ?', [$kecamatanLower])
            ->whereRaw('LOWER(TRIM(desa)) = ?', [$desaLower])
            ->get();

        // 3. Query Tabel BNBA
        $status = $request->get('status', 'all'); // 'all', 'layak', 'tidak_layak', 'mypkp', 'belum'
        $search = $request->get('search');
        $perPage = $request->get('per_page', '15');
        $perPageLimit = ($perPage === 'all') ? 10000 : (int)$perPage;

        $tableQuery = (clone $baseQuery)->with('petugas');

        if ($status === 'layak') {
            $tableQuery->where('status_kelayakan', 'Layak Diusulkan');
        } elseif ($status === 'tidak_layak') {
            $tableQuery->where('status_kelayakan', 'Tidak Layak Diusulkan');
        } elseif ($status === 'mypkp') {
            $tableQuery->where('is_mypkp', true);
        } elseif ($status === 'belum') {
            $tableQuery->belumSurvei();
        }

        if ($search) {
            $tableQuery->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_ktp', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $penerimaList = $tableQuery->orderBy('nama', 'asc')->paginate($perPageLimit)->withQueryString();

        // List Kecamatan & Desa untuk dropdown switcher
        $listKecamatan = DataPenerima::whereNotNull('kecamatan')
            ->where('kecamatan', '!=', '')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');

        $listDesaInKec = DataPenerima::whereRaw('LOWER(TRIM(kecamatan)) = ?', [$kecamatanLower])
            ->whereNotNull('desa_kelurahan')
            ->where('desa_kelurahan', '!=', '')
            ->distinct()
            ->orderBy('desa_kelurahan')
            ->pluck('desa_kelurahan');

        return view('dashboard.desa', compact(
            'kecamatanParam',
            'desaParam',
            'totalTarget',
            'totalSudah',
            'totalBelum',
            'totalLayak',
            'totalTidakLayak',
            'totalMypkp',
            'progresPercent',
            'persenLayak',
            'persenTidak',
            'backlog1',
            'backlog2',
            'petugasList',
            'status',
            'search',
            'perPage',
            'penerimaList',
            'listKecamatan',
            'listDesaInKec'
        ));
    }

    /**
     * Halaman Rekapitulasi Progres Capaian Target & Kelayakan 248 Desa/Kelurahan se-Kabupaten Jember
     */
    public function rekapDesa(Request $request)
    {
        $user = Auth::user();
        $kecamatan = $request->get('kecamatan');
        $search = $request->get('search');
        $perPage = $request->get('per_page', '20');
        $perPageLimit = ($perPage === 'all') ? 10000 : (int)$perPage;

        if ($user && $user->isAdminKecamatan()) {
            $kecamatan = $user->kecamatan;
        }

        $sudahSql = DataPenerima::getSudahSql();

        $desaStatsQuery = DataPenerima::selectRaw("
            kecamatan,
            desa_kelurahan,
            COUNT(*) as total_target,
            SUM(CASE WHEN {$sudahSql} THEN 1 ELSE 0 END) as total_sudah,
            (COUNT(*) - SUM(CASE WHEN {$sudahSql} THEN 1 ELSE 0 END)) as total_belum,
            SUM(CASE WHEN status_kelayakan = 'Layak Diusulkan' THEN 1 ELSE 0 END) as total_layak,
            SUM(CASE WHEN status_kelayakan = 'Tidak Layak Diusulkan' THEN 1 ELSE 0 END) as total_tidak_layak
        ")
        ->whereNotNull('kecamatan')
        ->where('kecamatan', '!=', '')
        ->whereNotNull('desa_kelurahan')
        ->where('desa_kelurahan', '!=', '');

        if ($kecamatan && $kecamatan !== 'all') {
            $desaStatsQuery->whereRaw('LOWER(TRIM(kecamatan)) = ?', [strtolower(trim($kecamatan))]);
        }

        if ($search) {
            $searchLower = '%' . strtolower(trim($search)) . '%';
            $desaStatsQuery->where(function($q) use ($searchLower) {
                $q->whereRaw('LOWER(desa_kelurahan) LIKE ?', [$searchLower])
                  ->orWhereRaw('LOWER(kecamatan) LIKE ?', [$searchLower]);
            });
        }

        $desaStatsQuery->groupBy('kecamatan', 'desa_kelurahan')
            ->orderBy('kecamatan', 'asc')
            ->orderBy('desa_kelurahan', 'asc');

        // Pagination for village rows
        $desaList = $desaStatsQuery->paginate($perPageLimit)->withQueryString();

        // Calculate progress percentage and percentages
        $desaList->getCollection()->transform(function ($d) {
            $target = (int)$d->total_target;
            $sudah = (int)$d->total_sudah;
            $layak = (int)$d->total_layak;
            $tidak = (int)$d->total_tidak_layak;

            $d->progres_percent = $target > 0 ? round(($sudah / $target) * 100, 1) : 0;
            $d->persen_layak = $sudah > 0 ? round(($layak / $sudah) * 100, 1) : 0;
            $d->persen_tidak = $sudah > 0 ? round(($tidak / $sudah) * 100, 1) : 0;
            return $d;
        });

        // Summary Counts Global
        $totalTargetGlobal = DataPenerima::count();
        $totalSudahGlobal = DataPenerima::sudahSurvei()->count();
        $totalBelumGlobal = max(0, $totalTargetGlobal - $totalSudahGlobal);
        $totalLayakGlobal = DataPenerima::where('status_kelayakan', 'Layak Diusulkan')->count();
        $totalTidakLayakGlobal = DataPenerima::where('status_kelayakan', 'Tidak Layak Diusulkan')->count();
        $totalDesaGlobal = DataPenerima::selectRaw("COUNT(DISTINCT CONCAT(kecamatan, ' - ', desa_kelurahan)) as total")->value('total');

        // List Kecamatan untuk filter
        $listKecamatan = DataPenerima::whereNotNull('kecamatan')
            ->where('kecamatan', '!=', '')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');

        return view('dashboard.rekap_desa', compact(
            'desaList',
            'listKecamatan',
            'kecamatan',
            'search',
            'perPage',
            'totalTargetGlobal',
            'totalSudahGlobal',
            'totalBelumGlobal',
            'totalLayakGlobal',
            'totalTidakLayakGlobal',
            'totalDesaGlobal'
        ));
    }
}
