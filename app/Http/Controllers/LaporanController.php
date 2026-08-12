<?php

namespace App\Http\Controllers;

use App\Models\DataPenerima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Halaman Utama Rekapitulasi Laporan BSPS Verval
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'rekap'); // rekap, indikator, galeri, detail
        $search = $request->get('search');
        $kecamatan = $request->get('kecamatan', 'all');
        $desa = $request->get('desa', 'all');
        $status = $request->get('status', 'all');
        $perPage = $request->get('per_page', 15);

        $user = Auth::user();
        if ($user) {
            if ($user->isAdminKecamatan() && $user->kecamatan) {
                $kecamatan = $user->kecamatan;
            } elseif ($user->isPetugas()) {
                if ($user->kecamatan) $kecamatan = $user->kecamatan;
                if ($user->desa) $desa = $user->desa;
            }
        }

        if ($perPage === 'all') {
            $perPageLimit = 999999;
        } else {
            $perPageLimit = in_array((int)$perPage, [10, 15, 20, 25, 50, 100]) ? (int)$perPage : 15;
        }

        // Base Query dengan Filter Role & Input User
        $query = DataPenerima::query();

        if ($kecamatan && $kecamatan !== 'all') {
            $query->whereRaw('LOWER(TRIM(kecamatan)) = ?', [strtolower(trim($kecamatan))]);
        }

        if ($desa && $desa !== 'all') {
            $query->whereRaw('LOWER(TRIM(desa_kelurahan)) = ?', [strtolower(trim($desa))]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_ktp', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('desa_kelurahan', 'like', "%{$search}%")
                  ->orWhere('kecamatan', 'like', "%{$search}%");
            });
        }

        if ($status === 'layak') {
            $query->where('status_kelayakan', 'Layak Diusulkan');
        } elseif ($status === 'tidak_layak') {
            $query->where('status_kelayakan', 'Tidak Layak Diusulkan');
        } elseif ($status === 'sudah') {
            $query->sudahSurvei();
        } elseif ($status === 'belum') {
            $query->belumSurvei();
        }

        // 1. STATISTIK RINGKASAN UTAMA
        $baseQuery = DataPenerima::query();
        if ($kecamatan && $kecamatan !== 'all') {
            $baseQuery->whereRaw('LOWER(TRIM(kecamatan)) = ?', [strtolower(trim($kecamatan))]);
        }
        if ($desa && $desa !== 'all') {
            $baseQuery->whereRaw('LOWER(TRIM(desa_kelurahan)) = ?', [strtolower(trim($desa))]);
        }

        $totalPenerima = (clone $baseQuery)->count();
        $totalSudahSurvei = (clone $baseQuery)->sudahSurvei()->count();
        $totalBelumSurvei = (clone $baseQuery)->belumSurvei()->count();
        $totalLayak = (clone $baseQuery)->where('status_kelayakan', 'Layak Diusulkan')->count();
        $totalTidakLayak = (clone $baseQuery)->where('status_kelayakan', 'Tidak Layak Diusulkan')->count();

        $stats = [
            'total_penerima'   => $totalPenerima,
            'sudah_survei'     => $totalSudahSurvei,
            'belum_survei'     => $totalBelumSurvei,
            'total_layak'      => $totalLayak,
            'total_tidak_layak'=> $totalTidakLayak,
            'persen_layak'     => $totalSudahSurvei > 0 ? round(($totalLayak / $totalSudahSurvei) * 100, 1) : 0,
        ];

        // Hitung Grand Total Capaian Indikator untuk Footer Tab Indikator
        $indTotals = (clone $baseQuery)
            ->selectRaw("
                COUNT(*) as total_penerima,
                SUM(CASE WHEN (foto_sudut_depan IS NOT NULL AND foto_sudut_depan != '') THEN 1 ELSE 0 END) as total_sudah_survei,
                SUM(CASE WHEN indikator_atap = 'tidak_ada' THEN 1 ELSE 0 END) as atap_rtlh,
                SUM(CASE WHEN indikator_dinding = 'tidak_ada' THEN 1 ELSE 0 END) as dinding_rtlh,
                SUM(CASE WHEN indikator_lantai = 'tidak_ada' THEN 1 ELSE 0 END) as lantai_rtlh,
                SUM(CASE WHEN indikator_pondasi = 'tidak_ada' THEN 1 ELSE 0 END) as pondasi_rtlh,
                SUM(CASE WHEN indikator_struktur = 'tidak_ada' THEN 1 ELSE 0 END) as struktur_rtlh,
                SUM(CASE WHEN indikator_penghasilan = 'ada' THEN 1 ELSE 0 END) as penghasilan_rtlh
            ")
            ->first();

        // 2. DROPDOWN LIST KECAMATAN & DESA
        if ($user && $user->isAdminKecamatan() && $user->kecamatan) {
            $listKecamatan = collect([$user->kecamatan]);
        } elseif ($user && $user->isPetugas() && $user->kecamatan) {
            $listKecamatan = collect([$user->kecamatan]);
        } else {
            $listKecamatan = DataPenerima::distinct()->whereNotNull('kecamatan')->where('kecamatan', '!=', '')->orderBy('kecamatan', 'asc')->pluck('kecamatan')->filter()->values();
        }

        $desaQuery = DataPenerima::distinct();
        if ($kecamatan && $kecamatan !== 'all') {
            $desaQuery->whereRaw('LOWER(TRIM(kecamatan)) = ?', [strtolower(trim($kecamatan))]);
        }
        $listDesa = $desaQuery->whereNotNull('desa_kelurahan')->where('desa_kelurahan', '!=', '')->orderBy('desa_kelurahan', 'asc')->pluck('desa_kelurahan')->filter()->values();

        // 3. AGREGASI BERDASARKAN TAB
        $rekapDesaKecamatan = null;
        $rekapIndikator = null;
        $penerimaList = null;

        // Konstruksi SQL Cek Kelengkapan Survei (Sesuai DataPenerima::$fieldWajibSurvei & Status Khusus)
        $conds = [];
        foreach (DataPenerima::$fieldWajibSurvei as $field) {
            $conds[] = "({$field} IS NOT NULL AND TRIM({$field}) != '')";
        }
        $formLengkapSql = "(" . implode(" AND ", $conds) . ")";
        $sudahSql = "(status IN ('meninggal', 'pindah', 'tidak diketahui') OR {$formLengkapSql})";

        if ($tab === 'rekap') {
            // TAB 1: REKAP HASIL SESUAI VS TIDAK SESUAI PER DESA & KECAMATAN
            $rekapQuery = DataPenerima::query();
            if ($kecamatan && $kecamatan !== 'all') {
                $rekapQuery->whereRaw('LOWER(TRIM(kecamatan)) = ?', [strtolower(trim($kecamatan))]);
            }
            if ($desa && $desa !== 'all') {
                $rekapQuery->whereRaw('LOWER(TRIM(desa_kelurahan)) = ?', [strtolower(trim($desa))]);
            }
            if ($search) {
                $rekapQuery->where(function($q) use ($search) {
                    $q->where('desa_kelurahan', 'like', "%{$search}%")
                      ->orWhere('kecamatan', 'like', "%{$search}%");
                });
            }

            $rekapDesaKecamatan = $rekapQuery
                ->selectRaw("
                    kecamatan,
                    desa_kelurahan,
                    COUNT(*) as total_penerima,
                    SUM(CASE WHEN status_kelayakan = 'Layak Diusulkan' THEN 1 ELSE 0 END) as total_layak,
                    SUM(CASE WHEN status_kelayakan = 'Tidak Layak Diusulkan' THEN 1 ELSE 0 END) as total_tidak_layak,
                    SUM(CASE WHEN {$sudahSql} THEN 1 ELSE 0 END) as total_sudah_survei
                ")
                ->groupBy('kecamatan', 'desa_kelurahan')
                ->orderBy('kecamatan')
                ->orderBy('desa_kelurahan')
                ->paginate($perPageLimit)
                ->withQueryString();

        } elseif ($tab === 'indikator') {
            // TAB 2: CAPAIAN 6 INDIKATOR RTLH PER DESA & KECAMATAN
            $indQuery = DataPenerima::query();
            if ($kecamatan && $kecamatan !== 'all') {
                $indQuery->whereRaw('LOWER(TRIM(kecamatan)) = ?', [strtolower(trim($kecamatan))]);
            }
            if ($desa && $desa !== 'all') {
                $indQuery->whereRaw('LOWER(TRIM(desa_kelurahan)) = ?', [strtolower(trim($desa))]);
            }
            if ($search) {
                $indQuery->where(function($q) use ($search) {
                    $q->where('desa_kelurahan', 'like', "%{$search}%")
                      ->orWhere('kecamatan', 'like', "%{$search}%");
                });
            }

            $rekapIndikator = $indQuery
                ->selectRaw("
                    kecamatan,
                    desa_kelurahan,
                    COUNT(*) as total_penerima,
                    SUM(CASE WHEN {$sudahSql} THEN 1 ELSE 0 END) as total_sudah_survei,
                    SUM(CASE WHEN indikator_atap = 'tidak_ada' THEN 1 ELSE 0 END) as atap_rtlh,
                    SUM(CASE WHEN indikator_dinding = 'tidak_ada' THEN 1 ELSE 0 END) as dinding_rtlh,
                    SUM(CASE WHEN indikator_lantai = 'tidak_ada' THEN 1 ELSE 0 END) as lantai_rtlh,
                    SUM(CASE WHEN indikator_pondasi = 'tidak_ada' THEN 1 ELSE 0 END) as pondasi_rtlh,
                    SUM(CASE WHEN indikator_struktur = 'tidak_ada' THEN 1 ELSE 0 END) as struktur_rtlh,
                    SUM(CASE WHEN indikator_penghasilan = 'ada' THEN 1 ELSE 0 END) as penghasilan_rtlh
                ")
                ->groupBy('kecamatan', 'desa_kelurahan')
                ->orderBy('kecamatan')
                ->orderBy('desa_kelurahan')
                ->paginate($perPageLimit)
                ->withQueryString();

        } else {
            // TAB 3 (GALERI FOTO) & TAB 4 (DETAIL PENERIMA)
            $penerimaList = $query->orderBy('nama', 'asc')->paginate($perPageLimit)->withQueryString();
        }

        return view('laporan.index', compact(
            'tab',
            'stats',
            'indTotals',
            'listKecamatan',
            'listDesa',
            'rekapDesaKecamatan',
            'rekapIndikator',
            'penerimaList',
            'perPage'
        ));
    }

    /**
     * Cetak Laporan Resmi A4 Landscape
     */
    public function cetak(Request $request)
    {
        $user = Auth::user();
        $kecamatan = $request->get('kecamatan', 'all');
        $desa = $request->get('desa', 'all');

        if ($user) {
            if ($user->isAdminKecamatan() && $user->kecamatan) {
                $kecamatan = $user->kecamatan;
            } elseif ($user->isPetugas()) {
                if ($user->kecamatan) $kecamatan = $user->kecamatan;
                if ($user->desa) $desa = $user->desa;
            }
        }

        $query = DataPenerima::query();
        if ($kecamatan && $kecamatan !== 'all') {
            $query->whereRaw('LOWER(TRIM(kecamatan)) = ?', [strtolower(trim($kecamatan))]);
        }
        if ($desa && $desa !== 'all') {
            $query->whereRaw('LOWER(TRIM(desa_kelurahan)) = ?', [strtolower(trim($desa))]);
        }

        $totalPenerima = (clone $query)->count();
        $totalSudahSurvei = (clone $query)->sudahSurvei()->count();
        $totalBelumSurvei = (clone $query)->belumSurvei()->count();
        $totalLayak = (clone $query)->where('status_kelayakan', 'Layak Diusulkan')->count();
        $totalTidakLayak = (clone $query)->where('status_kelayakan', 'Tidak Layak Diusulkan')->count();

        $stats = [
            'total'       => $totalPenerima,
            'sudah'       => $totalSudahSurvei,
            'belum'       => $totalBelumSurvei,
            'layak'       => $totalLayak,
            'tidak_layak' => $totalTidakLayak,
        ];

        $conds = [];
        foreach (DataPenerima::$fieldWajibSurvei as $field) {
            $conds[] = "({$field} IS NOT NULL AND TRIM({$field}) != '')";
        }
        $formLengkapSql = "(" . implode(" AND ", $conds) . ")";
        $sudahSql = "(status IN ('meninggal', 'pindah', 'tidak diketahui') OR {$formLengkapSql})";

        $rekapDesaKecamatan = (clone $query)
            ->selectRaw("
                kecamatan,
                desa_kelurahan,
                COUNT(*) as total_penerima,
                SUM(CASE WHEN {$sudahSql} THEN 1 ELSE 0 END) as total_sudah_survei,
                SUM(CASE WHEN status_kelayakan = 'Layak Diusulkan' THEN 1 ELSE 0 END) as total_layak,
                SUM(CASE WHEN status_kelayakan = 'Tidak Layak Diusulkan' THEN 1 ELSE 0 END) as total_tidak_layak,
                SUM(CASE WHEN indikator_atap = 'tidak_ada' THEN 1 ELSE 0 END) as atap_rtlh,
                SUM(CASE WHEN indikator_dinding = 'tidak_ada' THEN 1 ELSE 0 END) as dinding_rtlh,
                SUM(CASE WHEN indikator_lantai = 'tidak_ada' THEN 1 ELSE 0 END) as lantai_rtlh,
                SUM(CASE WHEN indikator_pondasi = 'tidak_ada' THEN 1 ELSE 0 END) as pondasi_rtlh,
                SUM(CASE WHEN indikator_struktur = 'tidak_ada' THEN 1 ELSE 0 END) as struktur_rtlh,
                SUM(CASE WHEN indikator_penghasilan = 'ada' THEN 1 ELSE 0 END) as penghasilan_rtlh
            ")
            ->groupBy('kecamatan', 'desa_kelurahan')
            ->orderBy('kecamatan')
            ->orderBy('desa_kelurahan')
            ->get();

        return view('laporan.cetak', compact('stats', 'rekapDesaKecamatan', 'kecamatan', 'desa'));
    }

    /**
     * Export Rekapitulasi Laporan ke Format Microsoft Excel (.XLS) dengan Styling Rapi & Foto Lapangan Embedded
     */
    public function exportExcel(Request $request)
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

        $user = Auth::user();
        $scope = $request->get('export_scope', 'all');
        $kecamatan = $request->get('kecamatan', 'all');
        $desa = $request->get('desa', 'all');
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        if ($user) {
            if ($user->isAdminKecamatan() && $user->kecamatan) {
                $kecamatan = $user->kecamatan;
            } elseif ($user->isPetugas()) {
                if ($user->kecamatan) $kecamatan = $user->kecamatan;
                if ($user->desa) $desa = $user->desa;
            }
        }

        $query = DataPenerima::query();
        if ($kecamatan && $kecamatan !== 'all') {
            $query->whereRaw('LOWER(TRIM(kecamatan)) = ?', [strtolower(trim($kecamatan))]);
        }
        if ($desa && $desa !== 'all') {
            $query->whereRaw('LOWER(TRIM(desa_kelurahan)) = ?', [strtolower(trim($desa))]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_ktp', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('desa_kelurahan', 'like', "%{$search}%")
                  ->orWhere('kecamatan', 'like', "%{$search}%");
            });
        }
        if ($status === 'layak') {
            $query->where('status_kelayakan', 'Layak Diusulkan');
        } elseif ($status === 'tidak_layak') {
            $query->where('status_kelayakan', 'Tidak Layak Diusulkan');
        } elseif ($status === 'sudah') {
            $query->sudahSurvei();
        } elseif ($status === 'belum') {
            $query->belumSurvei();
        }

        $totalPenerima = (clone $query)->count();
        $totalSudahSurvei = (clone $query)->sudahSurvei()->count();
        $totalBelumSurvei = (clone $query)->belumSurvei()->count();
        $totalLayak = (clone $query)->where('status_kelayakan', 'Layak Diusulkan')->count();
        $totalTidakLayak = (clone $query)->where('status_kelayakan', 'Tidak Layak Diusulkan')->count();

        $stats = [
            'total'       => $totalPenerima,
            'sudah'       => $totalSudahSurvei,
            'belum'       => $totalBelumSurvei,
            'layak'       => $totalLayak,
            'tidak_layak' => $totalTidakLayak,
        ];

        $conds = [];
        foreach (DataPenerima::$fieldWajibSurvei as $field) {
            $conds[] = "({$field} IS NOT NULL AND TRIM({$field}) != '')";
        }
        $formLengkapSql = "(" . implode(" AND ", $conds) . ")";
        $sudahSql = "(status IN ('meninggal', 'pindah', 'tidak diketahui') OR {$formLengkapSql})";

        $rekapDesaKecamatan = (clone $query)
            ->selectRaw("
                kecamatan,
                desa_kelurahan,
                COUNT(*) as total_penerima,
                SUM(CASE WHEN {$sudahSql} THEN 1 ELSE 0 END) as total_sudah_survei,
                SUM(CASE WHEN status_kelayakan = 'Layak Diusulkan' THEN 1 ELSE 0 END) as total_layak,
                SUM(CASE WHEN status_kelayakan = 'Tidak Layak Diusulkan' THEN 1 ELSE 0 END) as total_tidak_layak,
                SUM(CASE WHEN indikator_atap = 'tidak_ada' THEN 1 ELSE 0 END) as atap_rtlh,
                SUM(CASE WHEN indikator_dinding = 'tidak_ada' THEN 1 ELSE 0 END) as dinding_rtlh,
                SUM(CASE WHEN indikator_lantai = 'tidak_ada' THEN 1 ELSE 0 END) as lantai_rtlh,
                SUM(CASE WHEN indikator_pondasi = 'tidak_ada' THEN 1 ELSE 0 END) as pondasi_rtlh,
                SUM(CASE WHEN indikator_struktur = 'tidak_ada' THEN 1 ELSE 0 END) as struktur_rtlh,
                SUM(CASE WHEN indikator_penghasilan = 'ada' THEN 1 ELSE 0 END) as penghasilan_rtlh
            ")
            ->groupBy('kecamatan', 'desa_kelurahan')
            ->orderBy('kecamatan')
            ->orderBy('desa_kelurahan')
            ->get();

        // Data Detail Calon Penerima dengan Foto Lapangan
        $detailPenerima = (clone $query)->orderBy('kecamatan', 'asc')->orderBy('desa_kelurahan', 'asc')->orderBy('nama', 'asc')->get();

        // Resolve URL, File URI, dan Base64 untuk Foto Lapangan Excel
        $detailPenerima->transform(function ($item) {
            $depanData = $this->getPhotoInfo($item->foto_sudut_depan);
            $dalamData = $this->getPhotoInfo($item->foto_bagian_dalam);
            $ktpData   = $this->getPhotoInfo($item->ktp);

            $item->foto_depan_url    = $depanData['url'];
            $item->foto_depan_file_uri = $depanData['file_uri'];
            $item->foto_depan_base64 = $depanData['base64'];

            $item->foto_dalam_url    = $dalamData['url'];
            $item->foto_dalam_file_uri = $dalamData['file_uri'];
            $item->foto_dalam_base64 = $dalamData['base64'];

            $item->foto_ktp_url      = $ktpData['url'];
            $item->foto_ktp_file_uri   = $ktpData['file_uri'];
            $item->foto_ktp_base64   = $ktpData['base64'];

            return $item;
        });

        if ($desa && $desa !== 'all') {
            $prefix = 'rekap_bsps_desa_' . strtolower(str_replace([' ', '/', '\\'], '_', $desa));
        } elseif ($kecamatan && $kecamatan !== 'all') {
            $prefix = 'rekap_bsps_kec_' . strtolower(str_replace([' ', '/', '\\'], '_', $kecamatan));
        } else {
            $prefix = 'rekap_bsps_kab_jember_semua_desa';
        }
        $filename = $prefix . '_' . date('Ymd_His') . '.xls';

        $content = view('laporan.excel', compact(
            'stats',
            'rekapDesaKecamatan',
            'detailPenerima',
            'kecamatan',
            'desa'
        ))->render();

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ]);
    }

    /**
     * Helper Resolving Informasi Gambar (HTTP URL, Local File URI, & Base64)
     */
    private function getPhotoInfo($path)
    {
        $default = ['url' => null, 'file_uri' => null, 'base64' => null];
        if (empty($path)) return $default;

        $cleanPath = ltrim($path, '/');
        if (!str_starts_with($cleanPath, 'uploads/')) {
            $cleanPath = 'uploads/' . $cleanPath;
        }

        $possiblePaths = [
            public_path($cleanPath),
            base_path($cleanPath),
            storage_path('app/public/' . $cleanPath),
            storage_path('app/' . $cleanPath),
        ];

        $foundPath = null;
        foreach ($possiblePaths as $p) {
            if (file_exists($p) && !is_dir($p)) {
                $foundPath = $p;
                break;
            }
        }

        if (!$foundPath) {
            return $default;
        }

        // 1. Full HTTP URL untuk server web / Excel online
        $httpUrl = url($cleanPath);

        // 2. Absolute file:// URI untuk Excel lokal
        $fileUri = 'file:///' . str_replace('\\', '/', $foundPath);

        // 3. Base64
        $ext = strtolower(pathinfo($foundPath, PATHINFO_EXTENSION));
        $mime = $ext === 'png' ? 'image/png' : ($ext === 'webp' ? 'image/webp' : 'image/jpeg');
        $data = @file_get_contents($foundPath);
        $base64 = $data ? 'data:' . $mime . ';base64,' . base64_encode($data) : null;

        return [
            'url'      => $httpUrl,
            'file_uri' => $fileUri,
            'base64'   => $base64,
        ];
    }
}
