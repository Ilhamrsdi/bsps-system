<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bap;
use App\Models\DataMingguan;
use Illuminate\Support\Facades\Auth;

class BapController extends Controller
{
    public function index(Request $request)
    {
        $query = Bap::with('dataMingguan')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_bap', 'like', "%{$search}%")
                  ->orWhereHas('dataMingguan', fn($dq) =>
                      $dq->where('nama_kegiatan', 'like', "%{$search}%")
                         ->orWhere('lokasi', 'like', "%{$search}%")
                  );
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('lokasi') && $request->lokasi !== 'all') {
            $query->whereHas('dataMingguan', fn($dq) =>
                $dq->where('lokasi', $request->lokasi)
            );
        }

        if ($request->filled('periode') && $request->periode !== 'all') {
            // format: "Juli 2026" → filter by month/year
            $parts = explode(' ', $request->periode);
            if (count($parts) === 2) {
                $bulanMap = [
                    'Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,
                    'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,
                    'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12,
                ];
                $bulan = $bulanMap[$parts[0]] ?? null;
                $tahun = $parts[1];
                if ($bulan) {
                    $query->whereYear('created_at', $tahun)
                          ->whereMonth('created_at', $bulan);
                }
            }
        }

        $baps = $query->paginate(15)->withQueryString();

        $stats = [
            'total'  => Bap::count(),
            'terbit' => Bap::whereIn('status', ['terbit', 'selesai', 'ttd'])->count(),
            'draft'  => Bap::where('status', 'draft')->count(),
            'belum'  => DataMingguan::whereDoesntHave('bap')->count(),
        ];

        return view('bab.index', compact('baps', 'stats'));
    }

    /**
     * Generate BAP manual dari halaman BAP (pilih kegiatan yang belum punya BAP)
     */
    public function store(Request $request)
    {
        $request->validate([
            'data_mingguan_id' => 'required|exists:data_mingguans,id',
            'catatan'          => 'nullable|string',
        ]);

        // Cek apakah kegiatan sudah punya BAP
        $existing = Bap::where('data_mingguan_id', $request->data_mingguan_id)->first();
        if ($existing) {
            return back()->with('error', 'Kegiatan ini sudah memiliki BAP: ' . $existing->nomor_bap);
        }

        $bap = Bap::create([
            'nomor_bap'        => Bap::generateNomor(),
            'data_mingguan_id' => $request->data_mingguan_id,
            'status'           => 'draft',
            'catatan'          => $request->catatan,
            'user_id'          => Auth::id(),
        ]);

        // Update status_bap di kegiatan
        $bap->dataMingguan()->update(['status_bap' => 'sudah']);

        return back()->with('success', 'BAP berhasil digenerate dengan status Draft.');
    }

    /**
     * Generate BAP langsung dari halaman Data Mingguan
     */
    public function generateFromKegiatan(DataMingguan $dataMingguan)
    {
        if ($dataMingguan->status_bap === 'sudah' || $dataMingguan->bap) {
            return back()->with('error', 'Kegiatan ini sudah memiliki BAP.');
        }

        Bap::create([
            'nomor_bap'        => Bap::generateNomor(),
            'data_mingguan_id' => $dataMingguan->id,
            'status'           => 'draft',
            'user_id'          => Auth::id(),
        ]);

        $dataMingguan->update(['status_bap' => 'sudah']);

        return redirect()->route('bab')->with('success', 'BAP berhasil digenerate otomatis dari kegiatan!');
    }

    /**
     * Generate BAP untuk semua kegiatan yang belum memiliki BAP
     */
    public function generateAll()
    {
        $kegiatans = DataMingguan::whereDoesntHave('bap')->get();

        if ($kegiatans->isEmpty()) {
            return back()->with('error', 'Semua kegiatan sudah memiliki BAP.');
        }

        $count = 0;
        foreach ($kegiatans as $kegiatan) {
            Bap::create([
                'nomor_bap'        => Bap::generateNomor(),
                'data_mingguan_id' => $kegiatan->id,
                'status'           => 'draft',
                'user_id'          => Auth::id(),
            ]);

            $kegiatan->update(['status_bap' => 'sudah']);
            $count++;
        }

        return redirect()->route('bab')->with('success', "Berhasil men-generate {$count} BAP baru dengan status Draft.");
    }

    /**
     * Update status BAP (draft → terbit → ttd)
     */
    public function updateStatus(Request $request, Bap $bap)
    {
        $request->validate([
            'status' => 'required|in:draft,terbit,ttd,revisi',
        ]);

        $bap->update(['status' => $request->status]);

        return back()->with('success', 'Status BAP ' . $bap->nomor_bap . ' diperbarui.');
    }

    public function destroy(Bap $bap)
    {
        $nomor = $bap->nomor_bap;

        // Set status_bap di kegiatan kembali ke 'belum'
        if ($bap->dataMingguan) {
            $bap->dataMingguan->update(['status_bap' => 'belum']);
        }

        $bap->delete();
        return back()->with('success', 'BAP ' . $nomor . ' berhasil dihapus.');
    }
}
