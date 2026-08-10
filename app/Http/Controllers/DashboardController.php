<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DataMingguan;
use App\Models\Bap;
use App\Models\Survey;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::check() && Auth::user()->isPetugas()) {
            return redirect()->route('petugas.dashboard');
        }

        // Real Stats Database
        $totalKegiatan = DataMingguan::count();
        $surveiSelesai = Survey::count();
        $menungguSurvei = DataMingguan::whereDoesntHave('surveys')->count();
        $bapTerbit = Bap::count();

        // Pipeline Stats Real
        $pipeline = [
            'proses'   => DataMingguan::where('status', 'proses')->count(),
            'selesai'  => DataMingguan::where('status', 'selesai')->count(),
            'menunggu' => DataMingguan::where('status', 'menunggu')->count(),
            'survei'   => DataMingguan::where('status', 'survei')->count(),
            'bap'      => Bap::count(),
        ];

        // Chart Status Data Real
        $statusCounts = [
            'proses'   => DataMingguan::where('status', 'proses')->count(),
            'selesai'  => DataMingguan::where('status', 'selesai')->count(),
            'menunggu' => DataMingguan::where('status', 'menunggu')->count(),
            'survei'   => DataMingguan::where('status', 'survei')->count(),
            'batal'    => DataMingguan::where('status', 'batal')->count(),
        ];

        // Kegiatan Terbaru (5 item)
        $latestKegiatan = DataMingguan::with('surveys')->latest('updated_at')->take(5)->get();

        // Aktivitas Terbaru (Log Survei Petugas)
        $recentActivities = Survey::with(['dataMingguan', 'user'])->latest('updated_at')->take(5)->get();

        // Daftar Petugas
        $petugasList = User::where('role', 'petugas')->get();

        return view('dashboard.index', compact(
            'totalKegiatan',
            'surveiSelesai',
            'menungguSurvei',
            'bapTerbit',
            'pipeline',
            'statusCounts',
            'latestKegiatan',
            'recentActivities',
            'petugasList'
        ));
    }
}
