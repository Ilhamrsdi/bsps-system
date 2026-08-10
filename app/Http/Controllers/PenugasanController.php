<?php

namespace App\Http\Controllers;

use App\Models\DataPenerima;
use App\Models\User;
use Illuminate\Http\Request;

class PenugasanController extends Controller
{
    /**
     * Tampilkan Halaman Penugasan Petugas Verval (Menghubungkan Calon Penerima & Petugas Desa)
     */
    public function index(Request $request)
    {
        $search    = $request->get('search');
        $kecamatan = $request->get('kecamatan', 'all');
        $petugasId = $request->get('petugas_id', 'all');

        $query = DataPenerima::with('petugas');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_ktp', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('desa_kelurahan', 'like', "%{$search}%")
                  ->orWhere('kecamatan', 'like', "%{$search}%")
                  ->orWhereHas('petugas', function ($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($kecamatan && $kecamatan !== 'all') {
            $query->where('kecamatan', $kecamatan);
        }

        if ($petugasId && $petugasId !== 'all') {
            $query->where('user_id', $petugasId);
        }

        // Statistik Penugasan
        $totalPenerima   = DataPenerima::count();
        $totalDitugaskan = DataPenerima::whereNotNull('user_id')->count();
        $totalPetugas    = User::where('role', 'petugas')->count();
        $totalDesa       = DataPenerima::distinct('desa_kelurahan')->count('desa_kelurahan');

        $stats = [
            'total'          => $totalPenerima,
            'ditugaskan'     => $totalDitugaskan,
            'total_petugas'  => $totalPetugas,
            'desa'           => $totalDesa,
            'filter'         => $query->count(),
        ];

        // List Kecamatan & List Petugas untuk dropdown filter
        $listKecamatan = DataPenerima::distinct()->orderBy('kecamatan', 'asc')->pluck('kecamatan')->filter()->values();
        $listPetugas   = User::where('role', 'petugas')->orderBy('desa', 'asc')->get(['id', 'name', 'desa', 'kecamatan']);

        $vervals = $query->paginate(20)->withQueryString();

        return view('penugasan.index', compact('vervals', 'stats', 'listKecamatan', 'listPetugas'));
    }

    /**
     * Handler simpan / perbarui penugasan petugas
     */
    public function update(Request $request, $id = null)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id'
        ]);

        if ($id) {
            $penerima = DataPenerima::findOrFail($id);
            $penerima->update(['user_id' => $request->user_id]);
        }

        return redirect()->back()->with('success', 'Penugasan Petugas Verval berhasil diperbarui!');
    }
}
