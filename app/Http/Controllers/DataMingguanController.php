<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataMingguan;
use App\Models\Bap;
use Illuminate\Support\Facades\Auth;

class DataMingguanController extends Controller
{
    public function index(Request $request)
    {
        $query = DataMingguan::latest('tanggal');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kegiatan', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('kontraktor', 'like', "%{$search}%");
            });
        }
        if ($request->filled('lokasi') && $request->lokasi !== 'all') {
            $query->where('lokasi', $request->lokasi);
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('minggu') && $request->minggu !== 'all') {
            $query->where('minggu', $request->minggu);
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $data = $query->paginate(15)->withQueryString();

        $stats = [
            'total'    => DataMingguan::count(),
            'selesai'  => DataMingguan::where('status', 'selesai')->count(),
            'menunggu' => DataMingguan::where('status', 'menunggu')->count(),
            'proses'   => DataMingguan::where('status', 'proses')->count(),
        ];

        return view('data_mingguan.index', compact('data', 'stats'));
    }

    public function create()
    {
        return view('data_mingguan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'nama_pemohon'  => 'nullable|string|max:255',
            'nik_pemohon'   => 'nullable|string|max:16',
            'lokasi'        => 'required|string|max:100',
            'alamat'        => 'nullable|string',
            'tanggal'       => 'required|date',
            'status'        => 'required|in:proses,selesai,menunggu,survei,batal',
            'nilai_kontrak' => 'nullable|string|max:50',
            'kontraktor'    => 'nullable|string|max:255',
            'deskripsi'     => 'nullable|string',
        ]);

        // Hitung nomor minggu dari tanggal
        $validated['minggu']  = (int) date('W', strtotime($validated['tanggal']));
        $validated['user_id'] = Auth::id();

        DataMingguan::create($validated);

        return redirect()->route('data-mingguan')
            ->with('success', 'Data kegiatan berhasil ditambahkan!');
    }

    public function show(DataMingguan $dataMingguan)
    {
        return view('data_mingguan.show', compact('dataMingguan'));
    }

    public function edit(DataMingguan $dataMingguan)
    {
        return view('data_mingguan.create', compact('dataMingguan'));
    }

    public function update(Request $request, DataMingguan $dataMingguan)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'nama_pemohon'  => 'nullable|string|max:255',
            'nik_pemohon'   => 'nullable|string|max:16',
            'lokasi'        => 'required|string|max:100',
            'alamat'        => 'nullable|string',
            'tanggal'       => 'required|date',
            'status'        => 'required|in:proses,selesai,menunggu,survei,batal',
            'nilai_kontrak' => 'nullable|string|max:50',
            'kontraktor'    => 'nullable|string|max:255',
            'deskripsi'     => 'nullable|string',
        ]);

        $validated['minggu'] = (int) date('W', strtotime($validated['tanggal']));

        $dataMingguan->update($validated);

        return redirect()->route('data-mingguan')
            ->with('success', 'Data kegiatan berhasil diperbarui!');
    }

    public function destroy(DataMingguan $dataMingguan)
    {
        $dataMingguan->delete();

        return redirect()->route('data-mingguan')
            ->with('success', 'Data kegiatan berhasil dihapus!');
    }
}

