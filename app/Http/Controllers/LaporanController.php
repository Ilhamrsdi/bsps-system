<?php

namespace App\Http\Controllers;

use App\Models\DataPenerima;
use Barryvdh\DomPDF\Facade\Pdf;
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
        if ($user && $user->isAdminKecamatan()) {
            $kecamatan = $user->kecamatan;
        }

        if ($perPage === 'all') {
            $perPageLimit = 999999;
        } else {
            $perPageLimit = in_array((int)$perPage, [10, 15, 20, 25, 50, 100]) ? (int)$perPage : 15;
        }

        // Base Query dengan Filter Role & Input User
        $query = DataPenerima::query();

        if ($user && $user->isAdminKecamatan()) {
            $query->where('kecamatan', $user->kecamatan);
        } elseif ($kecamatan && $kecamatan !== 'all') {
            $query->where('kecamatan', $kecamatan);
        }

        if ($desa && $desa !== 'all') {
            $query->where('desa_kelurahan', $desa);
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
        if ($user && $user->isAdminKecamatan()) {
            $baseQuery->where('kecamatan', $user->kecamatan);
        } elseif ($kecamatan && $kecamatan !== 'all') {
            $baseQuery->where('kecamatan', $kecamatan);
        }
        if ($desa && $desa !== 'all') {
            $baseQuery->where('desa_kelurahan', $desa);
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
        if ($user && $user->isAdminKecamatan()) {
            $listKecamatan = collect([$user->kecamatan]);
        } else {
            $listKecamatan = DataPenerima::distinct()->orderBy('kecamatan', 'asc')->pluck('kecamatan')->filter()->values();
        }

        $desaQuery = DataPenerima::distinct();
        if ($kecamatan && $kecamatan !== 'all') {
            $desaQuery->where('kecamatan', $kecamatan);
        }
        $listDesa = $desaQuery->orderBy('desa_kelurahan', 'asc')->pluck('desa_kelurahan')->filter()->values();

        // Mapping Seluruh Desa per Kecamatan untuk Modal Cetak PDF
        $allDesaByKecamatan = DataPenerima::select('kecamatan', 'desa_kelurahan')
            ->distinct()
            ->orderBy('kecamatan', 'asc')
            ->orderBy('desa_kelurahan', 'asc')
            ->get()
            ->groupBy('kecamatan')
            ->map(function ($items) {
                return $items->pluck('desa_kelurahan')->values();
            });

        // 3. AGREGASI BERDASARKAN TAB
        $rekapDesaKecamatan = null;
        $rekapIndikator = null;
        $penerimaList = null;

        if ($tab === 'rekap') {
            // TAB 1: REKAP HASIL SESUAI VS TIDAK SESUAI PER DESA & KECAMATAN
            $rekapQuery = DataPenerima::query();
            if ($user && $user->isAdminKecamatan()) {
                $rekapQuery->where('kecamatan', $user->kecamatan);
            } elseif ($kecamatan && $kecamatan !== 'all') {
                $rekapQuery->where('kecamatan', $kecamatan);
            }
            if ($desa && $desa !== 'all') {
                $rekapQuery->where('desa_kelurahan', $desa);
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
                    SUM(CASE WHEN (foto_sudut_depan IS NOT NULL AND foto_sudut_depan != '') THEN 1 ELSE 0 END) as total_sudah_survei
                ")
                ->groupBy('kecamatan', 'desa_kelurahan')
                ->orderBy('kecamatan')
                ->orderBy('desa_kelurahan')
                ->paginate($perPageLimit)
                ->withQueryString();

        } elseif ($tab === 'indikator') {
            // TAB 2: CAPAIAN 6 INDIKATOR RTLH PER DESA & KECAMATAN
            $indQuery = DataPenerima::query();
            if ($user && $user->isAdminKecamatan()) {
                $indQuery->where('kecamatan', $user->kecamatan);
            } elseif ($kecamatan && $kecamatan !== 'all') {
                $indQuery->where('kecamatan', $kecamatan);
            }
            if ($desa && $desa !== 'all') {
                $indQuery->where('desa_kelurahan', $desa);
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
                    SUM(CASE WHEN (foto_sudut_depan IS NOT NULL AND foto_sudut_depan != '') THEN 1 ELSE 0 END) as total_sudah_survei,
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
            'allDesaByKecamatan',
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

        if ($user && $user->isAdminKecamatan()) {
            $kecamatan = $user->kecamatan;
        }

        $query = DataPenerima::query();
        if ($user && $user->isAdminKecamatan()) {
            $query->where('kecamatan', $user->kecamatan);
        } elseif ($kecamatan && $kecamatan !== 'all') {
            $query->where('kecamatan', $kecamatan);
        }
        if ($desa && $desa !== 'all') {
            $query->where('desa_kelurahan', $desa);
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

        $rekapDesaKecamatan = (clone $query)
            ->selectRaw("
                kecamatan,
                desa_kelurahan,
                COUNT(*) as total_penerima,
                SUM(CASE WHEN (foto_sudut_depan IS NOT NULL AND foto_sudut_depan != '') THEN 1 ELSE 0 END) as total_sudah_survei,
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
        $user = Auth::user();
        $scope = $request->get('export_scope', 'all');
        $kecamatan = $request->get('kecamatan', 'all');
        
        if ($scope === 'all' && (!$user || !$user->isAdminKecamatan())) {
            $kecamatan = 'all';
        }

        $desa = $request->get('desa', 'all');
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        if ($user && $user->isAdminKecamatan()) {
            $kecamatan = $user->kecamatan;
        }

        $query = DataPenerima::query();
        if ($user && $user->isAdminKecamatan()) {
            $query->where('kecamatan', $user->kecamatan);
        } elseif ($kecamatan && $kecamatan !== 'all') {
            $query->where('kecamatan', $kecamatan);
        }
        if ($desa && $desa !== 'all') {
            $query->where('desa_kelurahan', $desa);
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

        $rekapDesaKecamatan = (clone $query)
            ->selectRaw("
                kecamatan,
                desa_kelurahan,
                COUNT(*) as total_penerima,
                SUM(CASE WHEN (foto_sudut_depan IS NOT NULL AND foto_sudut_depan != '') THEN 1 ELSE 0 END) as total_sudah_survei,
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

        // Convert Foto ke Base64 Inline
        $detailPenerima->transform(function ($item) {
            $item->foto_depan_base64    = $this->fileToBase64($item->foto_sudut_depan);
            $item->foto_dalam_base64    = $this->fileToBase64($item->foto_bagian_dalam);
            $item->foto_ktp_base64      = $this->fileToBase64($item->ktp);
            return $item;
        });

        $prefix = ($kecamatan && $kecamatan !== 'all') 
            ? 'rekap_bsps_kec_' . strtolower(str_replace([' ', '/', '\\'], '_', $kecamatan)) 
            : 'rekap_bsps_kab_jember_semua_desa';
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
     * Export / Cetak Laporan Resmi per Desa dalam Format PDF Ukuran F4 (Folio)
     */
    public function exportPdfDesa(Request $request)
    {
        $user = Auth::user();
        $kecamatan = $request->get('kecamatan');
        $desa = $request->get('desa');
        $mode = $request->get('mode', 'stream'); // stream (buka di browser) atau download

        if ($user && $user->isAdminKecamatan()) {
            $kecamatan = $user->kecamatan;
        }

        if (!$kecamatan || $kecamatan === 'all') {
            $firstRecord = DataPenerima::first();
            $kecamatan = $firstRecord ? $firstRecord->kecamatan : 'AJUNG';
        }

        if (!$desa || $desa === 'all') {
            $firstDesaRecord = DataPenerima::where('kecamatan', $kecamatan)->first();
            $desa = $firstDesaRecord ? $firstDesaRecord->desa_kelurahan : '';
        }

        $query = DataPenerima::where('kecamatan', $kecamatan);
        if ($desa && $desa !== 'all') {
            $query->where('desa_kelurahan', $desa);
        }

        $penerimaList = (clone $query)->orderBy('nama', 'asc')->get();

        $totalPenerima = $penerimaList->count();
        $sudahSurvei = $penerimaList->filter(function($p) {
            return !empty($p->foto_sudut_depan);
        })->count();
        $belumSurvei = max(0, $totalPenerima - $sudahSurvei);
        $totalLayak = $penerimaList->where('status_kelayakan', 'Layak Diusulkan')->count();
        $totalTidakLayak = $penerimaList->where('status_kelayakan', 'Tidak Layak Diusulkan')->count();

        // Persentase Layak & Tidak Layak dari data yang disurvei
        $persenLayak = $sudahSurvei > 0 ? round(($totalLayak / $sudahSurvei) * 100, 1) : 0;
        $persenTidakLayak = $sudahSurvei > 0 ? round(($totalTidakLayak / $sudahSurvei) * 100, 1) : 0;
        $persenSurvei = $totalPenerima > 0 ? round(($sudahSurvei / $totalPenerima) * 100, 1) : 0;

        // Capaian Indikator di Desa
        $indAtap = $penerimaList->where('indikator_atap', 'tidak_ada')->count();
        $indDinding = $penerimaList->where('indikator_dinding', 'tidak_ada')->count();
        $indLantai = $penerimaList->where('indikator_lantai', 'tidak_ada')->count();
        $indPondasi = $penerimaList->where('indikator_pondasi', 'tidak_ada')->count();
        $indStruktur = $penerimaList->where('indikator_struktur', 'tidak_ada')->count();
        $indPenghasilan = $penerimaList->where('indikator_penghasilan', 'ada')->count();

        $stats = [
            'total' => $totalPenerima,
            'sudah' => $sudahSurvei,
            'belum' => $belumSurvei,
            'layak' => $totalLayak,
            'tidak_layak' => $totalTidakLayak,
            'persen_layak' => $persenLayak,
            'persen_tidak_layak' => $persenTidakLayak,
            'persen_survei' => $persenSurvei,
            'atap' => $indAtap,
            'dinding' => $indDinding,
            'lantai' => $indLantai,
            'pondasi' => $indPondasi,
            'struktur' => $indStruktur,
            'penghasilan' => $indPenghasilan,
        ];

        $logoPath = public_path('logo.jpg');
        $logoBase64 = file_exists($logoPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath)) : '';

        // Ambil Data Kepala Desa dari DB
        $kades = null;
        if ($desa && $desa !== 'all') {
            $kades = \App\Models\KepalaDesa::whereRaw('LOWER(kecamatan) = ?', [strtolower($kecamatan)])
                ->whereRaw('LOWER(desa_kelurahan) = ?', [strtolower($desa)])
                ->first();
        }

        // Ambil Data Petugas Survei Desa dari DB
        $petugas = null;
        if ($desa && $desa !== 'all') {
            $petugas = \App\Models\User::where('role', 'petugas')
                ->whereRaw('LOWER(kecamatan) = ?', [strtolower($kecamatan)])
                ->whereRaw('LOWER(desa) = ?', [strtolower($desa)])
                ->first();
        }
        if (!$petugas) {
            $firstWithPetugas = $penerimaList->first(function($p) { return !empty($p->user_id) && $p->petugas; });
            if ($firstWithPetugas) {
                $petugas = $firstWithPetugas->petugas;
            }
        }

        // Ukuran Kertas F4 (Folio) Portrait: 215mm x 330mm = 609.45pt x 935.43pt
        $pdf = Pdf::loadView('laporan.pdf_desa', compact(
            'kecamatan',
            'desa',
            'penerimaList',
            'stats',
            'logoBase64',
            'kades',
            'petugas'
        ))->setPaper([0, 0, 609.45, 935.43], 'portrait');

        $filename = 'Laporan_BSPS_Desa_' . strtolower(str_replace([' ', '/', '\\'], '_', $desa ?: 'semua')) . '_Kec_' . strtolower(str_replace([' ', '/', '\\'], '_', $kecamatan)) . '.pdf';

        if ($mode === 'download') {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }

    /**
     * Helper Konversi File Gambar Lokal / Uploads ke Base64 String
     */
    private function fileToBase64($path)
    {
        if (empty($path)) return null;

        $possiblePaths = [
            public_path(ltrim($path, '/')),
            base_path(ltrim($path, '/')),
            storage_path('app/public/' . ltrim($path, '/')),
            storage_path('app/' . ltrim($path, '/')),
        ];

        foreach ($possiblePaths as $p) {
            if (file_exists($p) && !is_dir($p)) {
                $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                $mime = $ext === 'png' ? 'image/png' : ($ext === 'webp' ? 'image/webp' : 'image/jpeg');
                $data = @file_get_contents($p);
                if ($data) {
                    return 'data:' . $mime . ';base64,' . base64_encode($data);
                }
            }
        }

        return null;
    }
}
