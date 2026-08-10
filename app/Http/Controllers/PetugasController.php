<?php

namespace App\Http\Controllers;

use App\Models\DataMingguan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasController extends Controller
{
    /**
     * Dashboard Utama Petugas Survei Lapangan
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        // Ambil kegiatan yang khusus ditugaskan oleh Admin kepada petugas ini
        $query = $user->kegiatans()->with(['surveys', 'user'])->latest('tanggal');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kegiatan', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('nama_pemohon', 'like', "%{$search}%");
            });
        }

        $allAssigned = $user->kegiatans()->get();
        $totalTugas = $allAssigned->count();
        $belumSurvei = $allAssigned->filter(fn($k) => $k->surveys->count() == 0)->count();
        $sudahSurvei = $allAssigned->filter(fn($k) => $k->surveys->count() > 0)->count();
        $persentaseSelesai = $totalTugas > 0 ? round(($sudahSurvei / $totalTugas) * 100) : 0;

        $stats = [
            'total_tugas'        => $totalTugas,
            'belum_survei'       => $belumSurvei,
            'sudah_survei'       => $sudahSurvei,
            'persentase_selesai' => $persentaseSelesai,
        ];

        $kegiatans = $query->paginate(10)->withQueryString();

        return view('petugas.dashboard', compact('kegiatans', 'stats'));
    }

    /**
     * Halaman Tugas Belum Di-survei
     */
    public function belumSurvei(Request $request)
    {
        $user = Auth::user();

        // Kegiatan ditugaskan yang BELUM memiliki entri survei
        $query = $user->kegiatans()->doesntHave('surveys')->latest('tanggal');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kegiatan', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('nama_pemohon', 'like', "%{$search}%");
            });
        }

        $kegiatans = $query->paginate(12)->withQueryString();

        return view('petugas.belum_survei', compact('kegiatans'));
    }

    /**
     * Halaman Tugas Sudah Di-survei
     */
    public function sudahSurvei(Request $request)
    {
        $user = Auth::user();

        // Kegiatan ditugaskan yang SUDAH memiliki entri survei
        $query = $user->kegiatans()->has('surveys')->with('surveys')->latest('tanggal');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kegiatan', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('nama_pemohon', 'like', "%{$search}%");
            });
        }

        $kegiatans = $query->paginate(12)->withQueryString();

        return view('petugas.sudah_survei', compact('kegiatans'));
    }

    /**
     * Update Lokasi GPS & IP milik Petugas saat mulai survei
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|string',
            'longitude' => 'required|string',
        ]);

        $userAgent = $request->userAgent() ?? '';
        $deviceType = 'Desktop / Laptop';

        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $userAgent)) {
            $deviceType = 'Tablet';
        } elseif (preg_match('/(Mobile|Android|iPhone|iPod|BlackBerry|IEMobile|Opera Mini)/i', $userAgent)) {
            $deviceType = 'Mobile Phone';
        }

        if ($request->filled('device_type')) {
            $deviceType = $request->device_type;
        }

        $user = Auth::user();
        if ($user) {
            $user->update([
                'latitude'         => $request->latitude,
                'longitude'        => $request->longitude,
                'last_ip'          => $request->ip(),
                'device_type'      => $deviceType,
                'user_agent'       => substr($userAgent, 0, 500),
                'last_location_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'device'  => $deviceType,
            'message' => 'Lokasi GPS, IP, dan tipe Perangkat (' . $deviceType . ') berhasil disimpan.'
        ]);
    }
}
