<?php

namespace App\Http\Controllers;

use App\Models\DataPenerima;
use Illuminate\Http\Request;

class PenugasanController extends Controller
{
    /**
     * Tampilkan Halaman Penugasan — Menampilkan Semua Data Verval Calon Penerima BSPS (12.673 Data)
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $kecamatan = $request->get('kecamatan', 'all');
        $desil = $request->get('desil', 'all');

        $query = DataPenerima::query();

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

        if ($kecamatan && $kecamatan !== 'all') {
            $query->where('kecamatan', $kecamatan);
        }

        if ($desil && $desil !== 'all') {
            $query->where('pengelompokan_desil', 'like', "%{$desil}%");
        }

        // Hitung statistik
        $totalPenerima = DataPenerima::count();
        $totalKecamatan = DataPenerima::distinct('kecamatan')->count('kecamatan');
        $totalDesa = DataPenerima::distinct('desa_kelurahan')->count('desa_kelurahan');

        $stats = [
            'total'     => $totalPenerima,
            'kecamatan' => $totalKecamatan,
            'desa'      => $totalDesa,
            'filter'    => $query->count(),
        ];

        // Daftar Kecamatan untuk filter dropdown
        $listKecamatan = DataPenerima::distinct()->orderBy('kecamatan', 'asc')->pluck('kecamatan')->filter()->values();

        $vervals = $query->paginate(20)->withQueryString();

        return view('penugasan.index', compact('vervals', 'stats', 'listKecamatan'));
    }

    /**
     * Simpan / Perbarui Penugasan (Handler opsional)
     */
    public function update(Request $request, $id = null)
    {
        return redirect()->back()->with('success', 'Data penugasan berhasil diperbarui!');
    }
}
