<?php

namespace App\Http\Controllers;

use App\Models\DataPenerima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // 4. Top Desa/Kelurahan dengan Usulan Terbanyak
        $topDesa = DataPenerima::selectRaw('desa_kelurahan, kecamatan, count(*) as total')
            ->groupBy('desa_kelurahan', 'kecamatan')
            ->orderByDesc('total')
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
        $conds = [];
        foreach (DataPenerima::$fieldWajibSurvei as $field) {
            $conds[] = "({$field} IS NOT NULL AND TRIM({$field}) != '')";
        }
        $formLengkapSql = "(" . implode(" AND ", $conds) . ")";
        $sudahSql = "(status IN ('meninggal', 'pindah', 'tidak diketahui') OR {$formLengkapSql})";

        $globalTotalTarget = $totalPenerima;
        $globalTotalSudah = DataPenerima::sudahSurvei()->count();
        $globalTotalBelum = max(0, $globalTotalTarget - $globalTotalSudah);
        $globalTotalLayak = DataPenerima::where('status_kelayakan', 'Layak Diusulkan')->count();
        $globalTotalTidakLayak = DataPenerima::where('status_kelayakan', 'Tidak Layak Diusulkan')->count();
        $globalPersenSurvei = $globalTotalTarget > 0 ? round(($globalTotalSudah / $globalTotalTarget) * 100, 1) : 0;
        $globalPersenLayak = $globalTotalSudah > 0 ? round(($globalTotalLayak / $globalTotalSudah) * 100, 1) : 0;

        $globalVervalStats = [
            'total_target' => $globalTotalTarget,
            'total_sudah' => $globalTotalSudah,
            'total_belum' => $globalTotalBelum,
            'total_layak' => $globalTotalLayak,
            'total_tidak_layak' => $globalTotalTidakLayak,
            'persen_survei' => $globalPersenSurvei,
            'persen_layak' => $globalPersenLayak,
        ];

        // Agregasi Desa & Kecamatan Lengkap (Semua Kecamatan & Seluruh Desa se-Kabupaten Jember)
        $desaStats = DataPenerima::selectRaw("
            kecamatan,
            desa_kelurahan,
            COUNT(*) as total_target,
            SUM(CASE WHEN {$sudahSql} THEN 1 ELSE 0 END) as total_sudah,
            SUM(CASE WHEN status_kelayakan = 'Layak Diusulkan' THEN 1 ELSE 0 END) as total_layak,
            SUM(CASE WHEN status_kelayakan = 'Tidak Layak Diusulkan' THEN 1 ELSE 0 END) as total_tidak_layak
        ")
        ->whereNotNull('kecamatan')
        ->where('kecamatan', '!=', '')
        ->whereNotNull('desa_kelurahan')
        ->where('desa_kelurahan', '!=', '')
        ->groupBy('kecamatan', 'desa_kelurahan')
        ->orderBy('kecamatan')
        ->orderBy('desa_kelurahan')
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
            'rekapPerKecamatan'
        ));
    }
}
