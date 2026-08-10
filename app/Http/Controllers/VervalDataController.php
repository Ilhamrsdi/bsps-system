<?php

namespace App\Http\Controllers;

use App\Models\DataPenerima;
use Illuminate\Http\Request;

class VervalDataController extends Controller
{
    /**
     * Tampilkan Halaman Daftar Data Verval Calon Penerima BSPS
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
                  ->orWhere('desa_kelurahan', 'like', "%{$search}%");
            });
        }

        if ($kecamatan && $kecamatan !== 'all') {
            $query->where('kecamatan', $kecamatan);
        }

        if ($desil && $desil !== 'all') {
            $query->where('pengelompokan_desil', 'like', "%{$desil}%");
        }

        // Hitung total penerima
        $totalPenerima = DataPenerima::distinct('no_ktp')->count('no_ktp');
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

        return view('verval_data.index', compact('vervals', 'stats', 'listKecamatan'));
    }
}
