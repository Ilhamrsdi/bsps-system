<?php

namespace App\Http\Controllers;

use App\Models\DataPenerima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasController extends Controller
{
    /**
     * Dashboard Khusus Petugas / Fasilitator Lapangan
     * Menampilkan data verval dari desa petugas yang login
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        // Ambil desa dan kecamatan petugas yang login
        $desaPetugas      = $user->desa ?? null;
        $kecamatanPetugas = $user->kecamatan ?? null;

        // Query data verval sesuai desa petugas
        $query = DataPenerima::query();
        if ($desaPetugas) {
            $query->where('desa_kelurahan', $desaPetugas);
        }

        $totalData = $query->count();

        // Stats berdasarkan data desa ini
        $backlog1 = (clone $query)->where('pengelompokan_desil', 'like', 'Backlog 1%')->count();
        $backlog2 = (clone $query)->where('pengelompokan_desil', 'like', 'Backlog 2%')->count();

        $stats = [
            'total_data'   => $totalData,
            'backlog1'     => $backlog1,
            'backlog2'     => $backlog2,
            'desa'         => $desaPetugas,
            'kecamatan'    => $kecamatanPetugas,
        ];

        // Filter & Search untuk tabel
        $search    = $request->get('search');
        $desilFilter = $request->get('desil', 'all');

        $vervals = DataPenerima::when($desaPetugas, function ($q) use ($desaPetugas) {
                        $q->where('desa_kelurahan', $desaPetugas);
                    })
                    ->when($search, function ($q) use ($search) {
                        $q->where(function ($sub) use ($search) {
                            $sub->where('nama_calon_penerima', 'like', "%$search%")
                                ->orWhere('nik', 'like', "%$search%")
                                ->orWhere('no_kk', 'like', "%$search%")
                                ->orWhere('alamat', 'like', "%$search%");
                        });
                    })
                    ->when($desilFilter !== 'all', function ($q) use ($desilFilter) {
                        $q->where('pengelompokan_desil', 'like', "$desilFilter%");
                    })
                    ->orderBy('id')
                    ->paginate(20)
                    ->withQueryString();

        return view('petugas.dashboard', compact('user', 'stats', 'vervals', 'search', 'desilFilter'));
    }

    /**
     * Halaman Tugas Belum Survei
     */
    public function belumSurvei()
    {
        $user = Auth::user();
        $desaPetugas = $user->desa ?? null;

        $kegiatans = DataPenerima::when($desaPetugas, function ($q) use ($desaPetugas) {
            $q->where('desa_kelurahan', $desaPetugas);
        })->limit(20)->get();

        return view('petugas.belum_survei', compact('kegiatans'));
    }

    /**
     * Halaman Tugas Sudah Survei
     */
    public function sudahSurvei()
    {
        $user = Auth::user();
        $desaPetugas = $user->desa ?? null;

        $kegiatans = DataPenerima::when($desaPetugas, function ($q) use ($desaPetugas) {
            $q->where('desa_kelurahan', $desaPetugas);
        })->limit(20)->get();

        return view('petugas.sudah_survei', compact('kegiatans'));
    }

    /**
     * Update Live GPS Location Petugas
     */
    public function updateLocation(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Koordinat GPS Petugas berhasil diperbarui.'
        ]);
    }
}
