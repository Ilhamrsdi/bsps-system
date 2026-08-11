<?php

namespace App\Http\Controllers;

use App\Models\DataPenerima;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardKecamatanController extends Controller
{
    /**
     * Tampilkan Dashboard Monitoring Khusus Admin Kecamatan
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Tentukan Kecamatan yang dipantau
        if ($user && $user->isAdminKecamatan()) {
            $kecamatanSelected = $user->kecamatan;
        } else {
            // Admin Kabupaten/Super Admin dapat memilih kecamatan via query param
            $kecamatanSelected = $request->get('kecamatan');
            if (!$kecamatanSelected || $kecamatanSelected === 'all') {
                $kecamatanSelected = $user->kecamatan ?: (DataPenerima::whereNotNull('kecamatan')->value('kecamatan') ?: 'KALIWATES');
            }
        }

        $query = DataPenerima::where('kecamatan', $kecamatanSelected);

        // 1. Kartu Ringkasan Utama
        $totalPenerima = (clone $query)->count();
        $totalDesa = (clone $query)->whereNotNull('desa_kelurahan')->distinct('desa_kelurahan')->count('desa_kelurahan');
        $totalSudahSurvei = (clone $query)->whereNotNull('foto_sudut_depan')->count();
        $totalBelumSurvei = (clone $query)->whereNull('foto_sudut_depan')->count();
        $progressPercent = $totalPenerima > 0 ? round(($totalSudahSurvei / $totalPenerima) * 100, 1) : 0;

        // 2. Breakdown Desil (Backlog 1 vs Backlog 2)
        $backlog1Count = (clone $query)->where('pengelompokan_desil', 'like', '%Backlog 1%')->count();
        $backlog2Count = (clone $query)->where('pengelompokan_desil', 'like', '%Backlog 2%')->count();

        // 3. Data Monitoring Rincian per Desa/Kelurahan
        $desaStats = DataPenerima::selectRaw('
                desa_kelurahan,
                COUNT(*) as total,
                SUM(CASE WHEN foto_sudut_depan IS NOT NULL THEN 1 ELSE 0 END) as sudah_survei,
                SUM(CASE WHEN foto_sudut_depan IS NULL THEN 1 ELSE 0 END) as belum_survei,
                SUM(CASE WHEN pengelompokan_desil LIKE "%Backlog 1%" THEN 1 ELSE 0 END) as backlog_1,
                SUM(CASE WHEN pengelompokan_desil LIKE "%Backlog 2%" THEN 1 ELSE 0 END) as backlog_2
            ')
            ->where('kecamatan', $kecamatanSelected)
            ->whereNotNull('desa_kelurahan')
            ->groupBy('desa_kelurahan')
            ->orderBy('desa_kelurahan', 'asc')
            ->get();

        // Attach jumlah petugas ke tiap desa
        $petugasPerDesa = User::where('kecamatan', $kecamatanSelected)->get()->groupBy('desa');
        $desaStats = $desaStats->map(function ($d) use ($petugasPerDesa) {
            $d->petugas_count = isset($petugasPerDesa[$d->desa_kelurahan]) ? count($petugasPerDesa[$d->desa_kelurahan]) : 0;
            $d->progress_percent = $d->total > 0 ? round(($d->sudah_survei / $d->total) * 100, 1) : 0;
            return $d;
        });

        // 4. Data untuk Chart.js (Grafik Desa)
        $chartDesaLabels = $desaStats->pluck('desa_kelurahan')->map(fn($d) => ucwords(strtolower($d)));
        $chartSudahData = $desaStats->pluck('sudah_survei');
        $chartBelumData = $desaStats->pluck('belum_survei');
        $chartBacklog1Data = $desaStats->pluck('backlog_1');
        $chartBacklog2Data = $desaStats->pluck('backlog_2');

        // 5. Daftar Petugas Lapangan di Kecamatan ini
        $petugasList = User::where('kecamatan', $kecamatanSelected)
            ->orderBy('name', 'asc')
            ->get();

        // 6. List Semua Kecamatan untuk Dropdown Filter (jika dipantau oleh Admin Super)
        $listKecamatan = DataPenerima::distinct('kecamatan')
            ->whereNotNull('kecamatan')
            ->orderBy('kecamatan', 'asc')
            ->pluck('kecamatan');

        return view('dashboard.kecamatan', compact(
            'kecamatanSelected',
            'totalPenerima',
            'totalDesa',
            'totalSudahSurvei',
            'totalBelumSurvei',
            'progressPercent',
            'backlog1Count',
            'backlog2Count',
            'desaStats',
            'chartDesaLabels',
            'chartSudahData',
            'chartBelumData',
            'chartBacklog1Data',
            'chartBacklog2Data',
            'petugasList',
            'listKecamatan'
        ));
    }
}
