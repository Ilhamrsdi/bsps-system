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
        if ($user && $user->isAdminKecamatan() && $user->kecamatan) {
            $baseQuery->whereRaw('LOWER(TRIM(kecamatan)) = ?', [strtolower(trim($user->kecamatan))]);
        } elseif ($kecamatan && $kecamatan !== 'all') {
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

        // Jika export scope 'all' (Seluruh Kabupaten), reset filter wilayah agar mengekspor seluruh 31 kecamatan
        if ($scope === 'all') {
            $kecamatan = 'all';
            $desa = 'all';
        } else {
            if ($user) {
                if ($user->isAdminKecamatan() && $user->kecamatan) {
                    $kecamatan = $user->kecamatan;
                } elseif ($user->isPetugas()) {
                    if ($user->kecamatan) $kecamatan = $user->kecamatan;
                    if ($user->desa) $desa = $user->desa;
                }
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

        $conds = [];
        foreach (DataPenerima::$fieldWajibSurvei as $field) {
            $conds[] = "({$field} IS NOT NULL AND TRIM({$field}) != '')";
        }
        $formLengkapSql = "(" . implode(" AND ", $conds) . ")";
        $sudahSql = "(status IN ('meninggal', 'pindah', 'tidak diketahui') OR {$formLengkapSql})";

        $rekapDesaKecamatan = (clone $query)
            ->whereNotNull('kecamatan')
            ->where('kecamatan', '!=', '')
            ->whereNotNull('desa_kelurahan')
            ->where('desa_kelurahan', '!=', '')
            ->selectRaw("
                kecamatan,
                desa_kelurahan,
                COUNT(*) as total_penerima,
                SUM(CASE WHEN status_kelayakan = 'Layak Diusulkan' THEN 1 ELSE 0 END) as total_layak,
                SUM(CASE WHEN status_kelayakan = 'Tidak Layak Diusulkan' THEN 1 ELSE 0 END) as total_tidak_layak,
                SUM(CASE WHEN {$sudahSql} THEN 1 ELSE 0 END) as total_sudah_survei,
                SUM(CASE WHEN pengelompokan_desil LIKE '%Usulan Baru%' THEN 1 ELSE 0 END) as usulan_baru,
                SUM(CASE WHEN pengelompokan_desil LIKE '%Backlog 1%' THEN 1 ELSE 0 END) as backlog_1,
                SUM(CASE WHEN pengelompokan_desil LIKE '%Backlog 2%' THEN 1 ELSE 0 END) as backlog_2
            ")
            ->groupBy('kecamatan', 'desa_kelurahan')
            ->orderBy('kecamatan', 'asc')
            ->orderBy('desa_kelurahan', 'asc')
            ->get();

        // Hitung progress survei dan kelayakan per desa
        $rekapDesaKecamatan = $rekapDesaKecamatan->map(function($item) {
            $item->progres_survei = $item->total_penerima > 0 ? round(($item->total_sudah_survei / $item->total_penerima) * 100, 1) : 0;
            $item->persen_layak = $item->total_sudah_survei > 0 ? round(($item->total_layak / $item->total_sudah_survei) * 100, 1) : 0;
            return $item;
        });

        $sumTotal = 0; $sumSudah = 0; $sumBelum = 0; $sumLayak = 0; $sumTidakLayak = 0; $sumUsulanBaru = 0; $sumBacklog1 = 0; $sumBacklog2 = 0;
        foreach ($rekapDesaKecamatan as $r) {
            $sumTotal += $r->total_penerima;
            $sumSudah += $r->total_sudah_survei;
            $sumBelum += max(0, $r->total_penerima - $r->total_sudah_survei);
            $sumLayak += $r->total_layak;
            $sumTidakLayak += $r->total_tidak_layak;
            $sumUsulanBaru += $r->usulan_baru;
            $sumBacklog1 += $r->backlog_1;
            $sumBacklog2 += $r->backlog_2;
        }

        $prefix = ($kecamatan && $kecamatan !== 'all') 
            ? 'rekap_progress_kec_' . strtolower(str_replace([' ', '/', '\\'], '_', $kecamatan)) 
            : 'rekap_progress_kab_jember_semua_desa';

        if (class_exists('\Shuchkin\SimpleXLSXGen')) {
            $data = [
                ['<center><b>No</b></center>', '<center><b>Kecamatan</b></center>', '<center><b>Desa / Kelurahan</b></center>', '<center><b>Total Usulan</b></center>', '<center><b>Sudah Survei</b></center>', '<center><b>Belum Survei</b></center>', '<center><b>Layak Diusulkan</b></center>', '<center><b>Tidak Layak</b></center>', '<center><b>Usulan Baru</b></center>', '<center><b>Backlog 1</b></center>', '<center><b>Backlog 2</b></center>', '<center><b>% Progress Survei</b></center>', '<center><b>% Kelayakan</b></center>']
            ];
            $noIdx = 1;
            foreach ($rekapDesaKecamatan as $r) {
                $b = max(0, $r->total_penerima - $r->total_sudah_survei);
                $data[] = [
                    $noIdx++,
                    $r->kecamatan,
                    $r->desa_kelurahan,
                    $r->total_penerima,
                    $r->total_sudah_survei,
                    $b,
                    $r->total_layak,
                    $r->total_tidak_layak,
                    $r->usulan_baru,
                    $r->backlog_1,
                    $r->backlog_2,
                    $r->progres_survei . '%',
                    $r->persen_layak . '%'
                ];
            }
            $data[] = [
                '<b>TOTAL:</b>',
                '',
                '',
                '<b>'.$sumTotal.'</b>',
                '<b>'.$sumSudah.'</b>',
                '<b>'.$sumBelum.'</b>',
                '<b>'.$sumLayak.'</b>',
                '<b>'.$sumTidakLayak.'</b>',
                '<b>'.$sumUsulanBaru.'</b>',
                '<b>'.$sumBacklog1.'</b>',
                '<b>'.$sumBacklog2.'</b>',
                '<b>'.($sumTotal > 0 ? round(($sumSudah/$sumTotal)*100,1) : 0) . '%</b>',
                '<b>'.($sumSudah > 0 ? round(($sumLayak/$sumSudah)*100,1) : 0) . '%</b>'
            ];
            $filenameXlsx = $prefix . '_' . date('Ymd_His') . '.xlsx';
            \Shuchkin\SimpleXLSXGen::fromArray($data)->downloadAs($filenameXlsx);
            exit;
        }

        // Native Microsoft Excel (.xls) dengan UTF-8 BOM & Styling Standar PUPR (Bisa dibuka di semua Excel tanpa package tambahan)
        $filename = $prefix . '_' . date('Ymd_His') . '.xls';
        $wilayahLabel = ($kecamatan && $kecamatan !== 'all') ? 'Kecamatan ' . strtoupper($kecamatan) : 'Seluruh 31 Kecamatan & Desa di Kab. Jember';
        $totalPersen = $sumTotal > 0 ? round(($sumSudah / $sumTotal) * 100, 1) : 0;
        $totalLayakPersen = $sumSudah > 0 ? round(($sumLayak / $sumSudah) * 100, 1) : 0;

        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0, no-cache, no-store, must-revalidate');
        header('Pragma: public');

        echo "\xEF\xBB\xBF"; // UTF-8 BOM agar terbaca sempurna di Microsoft Excel
        ?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; color: #1e293b; }
        table { border-collapse: collapse; width: 100%; }
        th { background-color: #002855; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #001833; padding: 8px 12px; }
        td { border: 1px solid #cbd5e1; padding: 6px 10px; font-size: 11pt; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .total-row { background-color: #f1f5f9; font-weight: bold; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="13" style="border:none; text-align:center; font-size:16pt; font-weight:bold; color:#002855; height:32px;">
                REKAPITULASI PROGRES SURVEI BSPS KABUPATEN JEMBER
            </td>
        </tr>
        <tr>
            <td colspan="13" style="border:none; text-align:center; font-size:11pt; color:#475569; height:24px;">
                Wilayah: <?= htmlspecialchars($wilayahLabel) ?> &bull; Total Usulan: <?= number_format($sumTotal) ?> Penerima &bull; Tanggal Ekspor: <?= date('d M Y, H:i') ?> WIB
            </td>
        </tr>
        <tr><td colspan="13" style="border:none; height:10px;"></td></tr>
        <thead>
            <tr>
                <th style="background-color:#002855; color:#ffffff; font-weight:bold; text-align:center; border:1px solid #001833;">No</th>
                <th style="background-color:#002855; color:#ffffff; font-weight:bold; text-align:center; border:1px solid #001833;">Kecamatan</th>
                <th style="background-color:#002855; color:#ffffff; font-weight:bold; text-align:center; border:1px solid #001833;">Desa / Kelurahan</th>
                <th style="background-color:#002855; color:#ffffff; font-weight:bold; text-align:center; border:1px solid #001833;">Total Usulan</th>
                <th style="background-color:#002855; color:#ffffff; font-weight:bold; text-align:center; border:1px solid #001833;">Sudah Survei</th>
                <th style="background-color:#002855; color:#ffffff; font-weight:bold; text-align:center; border:1px solid #001833;">Belum Survei</th>
                <th style="background-color:#002855; color:#ffffff; font-weight:bold; text-align:center; border:1px solid #001833;">Layak</th>
                <th style="background-color:#002855; color:#ffffff; font-weight:bold; text-align:center; border:1px solid #001833;">Tidak Layak</th>
                <th style="background-color:#002855; color:#ffffff; font-weight:bold; text-align:center; border:1px solid #001833;">Usulan Baru</th>
                <th style="background-color:#002855; color:#ffffff; font-weight:bold; text-align:center; border:1px solid #001833;">Backlog 1</th>
                <th style="background-color:#002855; color:#ffffff; font-weight:bold; text-align:center; border:1px solid #001833;">Backlog 2</th>
                <th style="background-color:#002855; color:#ffffff; font-weight:bold; text-align:center; border:1px solid #001833;">% Selesai Verval</th>
                <th style="background-color:#002855; color:#ffffff; font-weight:bold; text-align:center; border:1px solid #001833;">% Kelayakan</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($rekapDesaKecamatan as $r): ?>
                <?php 
                    $b = max(0, $r->total_penerima - $r->total_sudah_survei); 
                    $badgeBg = $r->progres_survei >= 100 ? '#dcfce7' : ($r->progres_survei > 0 ? '#fef3c7' : '#f1f5f9');
                    $badgeColor = $r->progres_survei >= 100 ? '#15803d' : ($r->progres_survei > 0 ? '#b45309' : '#64748b');
                ?>
                <tr>
                    <td style="text-align:center; border:1px solid #cbd5e1;"><?= $no++ ?></td>
                    <td style="border:1px solid #cbd5e1;"><?= htmlspecialchars($r->kecamatan) ?></td>
                    <td style="border:1px solid #cbd5e1; font-weight:bold;"><?= htmlspecialchars($r->desa_kelurahan) ?></td>
                    <td style="text-align:center; border:1px solid #cbd5e1; font-weight:bold;"><?= $r->total_penerima ?></td>
                    <td style="text-align:center; border:1px solid #cbd5e1; color:#15803d; font-weight:bold;"><?= $r->total_sudah_survei ?></td>
                    <td style="text-align:center; border:1px solid #cbd5e1; color:#b91c1c;"><?= $b ?></td>
                    <td style="text-align:center; border:1px solid #cbd5e1; color:#15803d;"><?= $r->total_layak ?></td>
                    <td style="text-align:center; border:1px solid #cbd5e1; color:#b91c1c;"><?= $r->total_tidak_layak ?></td>
                    <td style="text-align:center; border:1px solid #cbd5e1;"><?= $r->usulan_baru ?></td>
                    <td style="text-align:center; border:1px solid #cbd5e1;"><?= $r->backlog_1 ?></td>
                    <td style="text-align:center; border:1px solid #cbd5e1;"><?= $r->backlog_2 ?></td>
                    <td style="text-align:center; border:1px solid #cbd5e1; font-weight:bold; background-color:<?= $badgeBg ?>; color:<?= $badgeColor ?>;">
                        <?= $r->progres_survei ?>%
                    </td>
                    <td style="text-align:center; border:1px solid #cbd5e1; font-weight:bold; color:#002855;">
                        <?= $r->persen_layak ?>%
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background-color:#e2e8f0; font-weight:bold;">
                <td colspan="3" style="text-align:center; border:1px solid #94a3b8; font-weight:bold; padding:8px; font-size:11pt;">TOTAL KESELURUHAN:</td>
                <td style="text-align:center; border:1px solid #94a3b8; font-weight:bold;"><?= number_format($sumTotal) ?></td>
                <td style="text-align:center; border:1px solid #94a3b8; font-weight:bold; color:#15803d;"><?= number_format($sumSudah) ?></td>
                <td style="text-align:center; border:1px solid #94a3b8; font-weight:bold; color:#b91c1c;"><?= number_format($sumBelum) ?></td>
                <td style="text-align:center; border:1px solid #94a3b8; font-weight:bold; color:#15803d;"><?= number_format($sumLayak) ?></td>
                <td style="text-align:center; border:1px solid #94a3b8; font-weight:bold; color:#b91c1c;"><?= number_format($sumTidakLayak) ?></td>
                <td style="text-align:center; border:1px solid #94a3b8; font-weight:bold;"><?= number_format($sumUsulanBaru) ?></td>
                <td style="text-align:center; border:1px solid #94a3b8; font-weight:bold;"><?= number_format($sumBacklog1) ?></td>
                <td style="text-align:center; border:1px solid #94a3b8; font-weight:bold;"><?= number_format($sumBacklog2) ?></td>
                <td style="text-align:center; border:1px solid #94a3b8; font-weight:bold; background-color:#cbd5e1; color:#002855;">
                    <?= $totalPersen ?>%
                </td>
                <td style="text-align:center; border:1px solid #94a3b8; font-weight:bold; background-color:#cbd5e1; color:#002855;">
                    <?= $totalLayakPersen ?>%
                </td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
        <?php
        exit;
    }

    /**
     * Helper Resolving Informasi Gambar (HTTP URL, Local File URI, & Base64)
    /**
     * Helper Konversi File Gambar Lokal / Uploads ke Base64 String
     */
    private function getPhotoInfo($path)
    {
        $default = ['url' => null, 'file_uri' => null, 'base64' => null];
        if (empty($path)) return $default;

        $cleanPath = ltrim(str_replace('\\', '/', $path), '/');
        $baseName  = basename($cleanPath);

        $possiblePaths = [
            public_path($cleanPath) => $cleanPath,
            public_path('uploads/' . $baseName) => 'uploads/' . $baseName,
            storage_path('app/public/' . $cleanPath) => 'storage/' . $cleanPath,
            storage_path('app/public/uploads/' . $baseName) => 'storage/uploads/' . $baseName,
            base_path($cleanPath) => $cleanPath,
        ];

        $foundDiskPath = null;
        $foundRelativeUrl = null;

        foreach ($possiblePaths as $diskPath => $relUrl) {
            if (file_exists($diskPath) && !is_dir($diskPath)) {
                $foundDiskPath = $diskPath;
                $foundRelativeUrl = $relUrl;
                break;
            }
        }

        if (!$foundDiskPath) {
            return $default;
        }

        // 1. Full HTTP URL untuk server web / Excel
        $httpUrl = url($foundRelativeUrl);

        // 2. Absolute file:// URI untuk Excel lokal
        $fileUri = 'file:///' . str_replace('\\', '/', $foundDiskPath);

        // 3. Base64
        $ext = strtolower(pathinfo($foundDiskPath, PATHINFO_EXTENSION));
        $mime = $ext === 'png' ? 'image/png' : ($ext === 'webp' ? 'image/webp' : 'image/jpeg');
        $data = @file_get_contents($foundDiskPath);
        $base64 = $data ? 'data:' . $mime . ';base64,' . base64_encode($data) : null;

        return [
            'url'      => $httpUrl,
            'file_uri' => $fileUri,
            'base64'   => $base64,
        ];
    }
}
