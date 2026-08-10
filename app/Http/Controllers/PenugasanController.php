<?php

namespace App\Http\Controllers;

use App\Models\DataMingguan;
use App\Models\User;
use Illuminate\Http\Request;

class PenugasanController extends Controller
{
    /**
     * Tampilkan Halaman Penugasan Petugas Survei Lapangan
     */
    public function index(Request $request)
    {
        $query = DataMingguan::with('petugas')->latest('tanggal');

        // Filter Pencarian Keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kegiatan', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('nama_pemohon', 'like', "%{$search}%")
                  ->orWhereHas('petugas', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%")
                         ->orWhere('nip', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Status Penugasan (sudah/belum)
        if ($request->filled('status_penugasan') && $request->status_penugasan !== 'all') {
            if ($request->status_penugasan === 'sudah') {
                $query->has('petugas');
            } elseif ($request->status_penugasan === 'belum') {
                $query->doesntHave('petugas');
            }
        }

        // Filter Lokasi Kecamatan
        if ($request->filled('lokasi') && $request->lokasi !== 'all') {
            $query->where('lokasi', $request->lokasi);
        }

        // Filter Status Kegiatan
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $kegiatans = $query->paginate(15)->withQueryString();

        // Ambil Semua User bertipe Role 'petugas' untuk opsi Select2 / Multi-Select modal
        $allPetugas = User::where('role', 'petugas')->orderBy('name', 'asc')->get();

        // Statistik Penugasan
        $stats = [
            'total_kegiatan'    => DataMingguan::count(),
            'sudah_ditugaskan'  => DataMingguan::has('petugas')->count(),
            'belum_ditugaskan'  => DataMingguan::doesntHave('petugas')->count(),
            'total_petugas'     => User::where('role', 'petugas')->count(),
        ];

        return view('penugasan.index', compact('kegiatans', 'allPetugas', 'stats'));
    }

    /**
     * Simpan / Perbarui Petugas yang Ditugaskan untuk Suatu Kegiatan
     */
    public function update(Request $request, DataMingguan $dataMingguan)
    {
        $validated = $request->validate([
            'petugas_ids'   => 'nullable|array|max:2',
            'petugas_ids.*' => 'exists:users,id',
        ], [
            'petugas_ids.max' => 'Maksimal 2 petugas survei yang dapat ditugaskan per kegiatan.',
        ]);

        $petugasIds = $request->input('petugas_ids', []);

        // Sync hubungan pivot table penugasans
        $dataMingguan->petugas()->sync($petugasIds);

        $count = count($petugasIds);
        $msg = $count > 0 
            ? "Penugasan berhasil diperbarui! {$count} petugas survei ditugaskan untuk kegiatan \"{$dataMingguan->nama_kegiatan}\"."
            : "Penugasan untuk kegiatan \"{$dataMingguan->nama_kegiatan}\" telah dikosongkan.";

        return redirect()->back()->with('success', $msg);
    }
}
