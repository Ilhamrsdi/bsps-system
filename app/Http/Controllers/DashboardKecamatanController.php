<?php

namespace App\Http\Controllers;

use App\Models\DataPenerima;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardKecamatanController extends Controller
{
    /**
     * Tampilkan Dashboard Monitoring Khusus Admin Kecamatan & Super Admin
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Tentukan Kecamatan yang dipantau
        if ($user && $user->isAdminKecamatan() && $user->kecamatan) {
            $kecamatanSelected = $user->kecamatan;
        } else {
            // Admin Kabupaten / Super Admin dapat memilih kecamatan via query param
            $paramKec = $request->get('kecamatan');
            if ($paramKec && $paramKec !== 'all') {
                $exists = DataPenerima::whereRaw('LOWER(TRIM(kecamatan)) = ?', [strtolower(trim($paramKec))])->exists();
                if ($exists) {
                    $kecamatanSelected = $paramKec;
                }
            }

            if (!isset($kecamatanSelected)) {
                // Gunakan kecamatan user jika ada dan valid di DB
                if ($user && $user->kecamatan && $user->kecamatan !== 'Jember' && DataPenerima::whereRaw('LOWER(TRIM(kecamatan)) = ?', [strtolower(trim($user->kecamatan))])->exists()) {
                    $kecamatanSelected = $user->kecamatan;
                } else {
                    // Default ke Kecamatan pertama dari database yang memiliki data
                    $kecamatanSelected = DataPenerima::whereNotNull('kecamatan')
                        ->where('kecamatan', '!=', '')
                        ->orderBy('kecamatan', 'asc')
                        ->value('kecamatan') ?: 'AJUNG';
                }
            }
        }

        $kecamatanLower = strtolower(trim($kecamatanSelected));
        $query = DataPenerima::whereRaw('LOWER(TRIM(kecamatan)) = ?', [$kecamatanLower]);

        // 2. Kartu Ringkasan Utama
        $totalPenerima = (clone $query)->count();
        $totalDesa = (clone $query)->whereNotNull('desa_kelurahan')->where('desa_kelurahan', '!=', '')->distinct('desa_kelurahan')->count('desa_kelurahan');
        $totalSudahSurvei = (clone $query)->sudahSurvei()->count();
        $totalBelumSurvei = (clone $query)->belumSurvei()->count();
        $progressPercent = $totalPenerima > 0 ? round(($totalSudahSurvei / $totalPenerima) * 100, 1) : 0;

        // 3. Breakdown Desil (Backlog 1 vs Backlog 2)
        $backlog1Count = (clone $query)->where('pengelompokan_desil', 'like', '%Backlog 1%')->count();
        $backlog2Count = (clone $query)->where('pengelompokan_desil', 'like', '%Backlog 2%')->count();

        $desaSelected = $request->get('desa', 'all');

        // 4. Data Monitoring Rincian per Desa/Kelurahan
        $conds = [];
        foreach (DataPenerima::$fieldWajibSurvei as $field) {
            $conds[] = "({$field} IS NOT NULL AND TRIM({$field}) != '')";
        }
        $formLengkapSql = "(" . implode(" AND ", $conds) . ")";
        $sudahSql = "(status IN ('meninggal', 'pindah', 'tidak diketahui', 'menolak disurvey') OR {$formLengkapSql})";

        $desaStatsQuery = DataPenerima::selectRaw("
                desa_kelurahan,
                COUNT(*) as total,
                SUM(CASE WHEN {$sudahSql} THEN 1 ELSE 0 END) as sudah_survei,
                (COUNT(*) - SUM(CASE WHEN {$sudahSql} THEN 1 ELSE 0 END)) as belum_survei,
                SUM(CASE WHEN pengelompokan_desil LIKE '%Backlog 1%' THEN 1 ELSE 0 END) as backlog_1,
                SUM(CASE WHEN pengelompokan_desil LIKE '%Backlog 2%' THEN 1 ELSE 0 END) as backlog_2
            ")
            ->whereRaw('LOWER(TRIM(kecamatan)) = ?', [$kecamatanLower])
            ->whereNotNull('desa_kelurahan')
            ->where('desa_kelurahan', '!=', '');

        if ($desaSelected && $desaSelected !== 'all') {
            $desaStatsQuery->whereRaw('LOWER(TRIM(desa_kelurahan)) = ?', [strtolower(trim($desaSelected))]);
        }

        $desaStats = $desaStatsQuery->groupBy('desa_kelurahan')
            ->orderBy('desa_kelurahan', 'asc')
            ->get();

        // Attach jumlah petugas ke tiap desa (dengan matching case-insensitive)
        $allPetugasInKec = User::whereRaw('LOWER(TRIM(kecamatan)) = ?', [$kecamatanLower])->get();
        $petugasPerDesa = $allPetugasInKec->groupBy(function ($u) {
            return strtolower(trim($u->desa));
        });

        $desaStats = $desaStats->map(function ($d) use ($petugasPerDesa) {
            $key = strtolower(trim($d->desa_kelurahan));
            $d->petugas_count = isset($petugasPerDesa[$key]) ? count($petugasPerDesa[$key]) : 0;
            $d->progress_percent = $d->total > 0 ? round(($d->sudah_survei / $d->total) * 100, 1) : 0;
            return $d;
        });

        // 5. Data untuk Chart.js (Grafik Desa)
        $chartDesaLabels = $desaStats->pluck('desa_kelurahan')->map(fn($d) => ucwords(strtolower($d)));
        $chartSudahData = $desaStats->pluck('sudah_survei');
        $chartBelumData = $desaStats->pluck('belum_survei');
        $chartBacklog1Data = $desaStats->pluck('backlog_1');
        $chartBacklog2Data = $desaStats->pluck('backlog_2');

        // 6. Daftar Petugas Lapangan di Kecamatan ini
        $petugasQuery = User::whereRaw('LOWER(TRIM(kecamatan)) = ?', [$kecamatanLower]);
        if ($desaSelected && $desaSelected !== 'all') {
            $petugasQuery->where(function($q) use ($desaSelected) {
                $q->whereRaw('LOWER(TRIM(desa)) = ?', [strtolower(trim($desaSelected))])
                  ->orWhereNull('desa')
                  ->orWhere('desa', '');
            });
        }
        $petugasList = $petugasQuery->orderBy('name', 'asc')->get();

        // 7. List Semua Kecamatan & Desa untuk Dropdown Filter
        $listKecamatan = DataPenerima::distinct('kecamatan')
            ->whereNotNull('kecamatan')
            ->where('kecamatan', '!=', '')
            ->orderBy('kecamatan', 'asc')
            ->pluck('kecamatan');

        $listDesa = DataPenerima::whereRaw('LOWER(TRIM(kecamatan)) = ?', [$kecamatanLower])
            ->whereNotNull('desa_kelurahan')
            ->where('desa_kelurahan', '!=', '')
            ->distinct('desa_kelurahan')
            ->orderBy('desa_kelurahan', 'asc')
            ->pluck('desa_kelurahan');

        return view('dashboard.kecamatan', compact(
            'kecamatanSelected',
            'desaSelected',
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
            'listKecamatan',
            'listDesa'
        ));
    }
}
