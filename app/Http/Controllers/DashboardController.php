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

        // 2. Statistik Desil Global (Backlog 1 vs Backlog 2)
        $desilStats = DataPenerima::selectRaw('pengelompokan_desil, count(*) as total')
            ->groupBy('pengelompokan_desil')
            ->pluck('total', 'pengelompokan_desil');

        $backlog1Count = $desilStats['Backlog 1 Desil 1-4'] ?? 0;
        $backlog2Count = $desilStats['Backlog 2 Desil 1-4'] ?? 0;

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

        return view('dashboard.index', compact(
            'totalPenerima',
            'totalKecamatan',
            'totalDesa',
            'topKecamatan',
            'topDesa',
            'backlog1Count',
            'backlog2Count',
            'lakiCount',
            'perempuanCount',
            'latestCandidates',
            'chartKecamatanLabels',
            'chartBacklog1Data',
            'chartBacklog2Data',
            'chartTotalData'
        ));
    }
}
