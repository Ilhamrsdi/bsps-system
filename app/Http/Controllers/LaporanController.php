<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataMingguan;
use App\Models\Survey;
use App\Models\Bap;
use App\Models\User;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'progress');
        $search = $request->get('search');
        $kecamatan = $request->get('kecamatan');
        $statusBap = $request->get('status_bap');
        $bulan = $request->get('bulan');

        // Stats Counters Dinamis
        $totalKegiatan = DataMingguan::count();
        $totalBapTerbit = Bap::where('status', 'terbit')->count();
        $totalBelumBap = DataMingguan::doesntHave('bap')->count();
        $totalSurveiSelesai = DataMingguan::has('surveys')->count();

        $stats = [
            'total_kegiatan' => $totalKegiatan,
            'bap_terbit'     => $totalBapTerbit,
            'belum_bap'      => $totalBelumBap,
            'survei_selesai' => $totalSurveiSelesai,
        ];

        // Query Utama Kegiatan & Data Mingguan
        $queryKegiatan = DataMingguan::with(['user', 'bap', 'petugas', 'surveys']);

        if ($search) {
            $queryKegiatan->where(function($q) use ($search) {
                $q->where('nama_kegiatan', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('kontraktor', 'like', "%{$search}%");
            });
        }

        if ($kecamatan && $kecamatan !== 'all') {
            $queryKegiatan->where('lokasi', 'like', "%{$kecamatan}%");
        }

        if ($statusBap && $statusBap !== 'all') {
            if ($statusBap === 'terbit') {
                $queryKegiatan->whereHas('bap', fn($q) => $q->where('status', 'terbit'));
            } elseif ($statusBap === 'draft') {
                $queryKegiatan->whereHas('bap', fn($q) => $q->where('status', 'draft'));
            } elseif ($statusBap === 'belum') {
                $queryKegiatan->doesntHave('bap');
            }
        }

        if ($bulan && $bulan !== 'all') {
            $queryKegiatan->whereMonth('tanggal', $bulan);
        }

        $kegiatans = $queryKegiatan->latest()->paginate(15)->appends($request->all());

        // Tab Data Khusus
        $surveylist = null;
        if ($tab === 'survei') {
            $querySurvei = Survey::with(['dataMingguan', 'user']);
            if ($search) {
                $querySurvei->whereHas('dataMingguan', function($q) use ($search) {
                    $q->where('nama_kegiatan', 'like', "%{$search}%");
                })->orWhereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }
            $surveylist = $querySurvei->latest()->paginate(15)->appends($request->all());
        }

        $baplist = null;
        if ($tab === 'bap') {
            $queryBap = Bap::with(['dataMingguan', 'user']);
            if ($search) {
                $queryBap->where('nomor_bap', 'like', "%{$search}%")
                         ->orWhereHas('dataMingguan', fn($q) => $q->where('nama_kegiatan', 'like', "%{$search}%"));
            }
            $baplist = $queryBap->latest()->paginate(15)->appends($request->all());
        }

        $petugaslist = null;
        if ($tab === 'petugas') {
            $queryPetugas = User::whereIn('role', ['petugas', 'admin'])->with(['kegiatans', 'surveys']);
            if ($search) {
                $queryPetugas->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%")
                      ->orWhere('kecamatan', 'like', "%{$search}%");
                });
            }
            $petugaslist = $queryPetugas->paginate(15)->appends($request->all());
        }

        return view('laporan.index', compact(
            'stats',
            'tab',
            'kegiatans',
            'surveylist',
            'baplist',
            'petugaslist'
        ));
    }

    public function exportExcel(Request $request)
    {
        $kegiatans = DataMingguan::with(['bap', 'petugas', 'surveys'])->get();

        $filename = "Rekap_Laporan_PUPR_Jember_" . date('Y-m-d') . ".csv";

        $handle = fopen('php://output', 'w');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Header CSV
        fputcsv($handle, [
            'No',
            'Nama Kegiatan Lapangan',
            'Lokasi / Kecamatan',
            'Alamat',
            'Tanggal Kegiatan',
            'Nilai Kontrak (Rp)',
            'Kontraktor / Pelaksana',
            'Status BAP',
            'Jumlah Survei Lapangan',
            'Jumlah Petugas Ditugaskan'
        ]);

        foreach ($kegiatans as $index => $item) {
            $statusBapLabel = $item->bap ? ucfirst($item->bap->status) : 'Belum Memiliki BAP';
            fputcsv($handle, [
                $index + 1,
                $item->nama_kegiatan,
                'Kec. ' . ucwords(str_replace('_', ' ', $item->lokasi)),
                $item->alamat ?: '-',
                $item->tanggal ? $item->tanggal->format('d/m/Y') : '-',
                $item->nilai_kontrak ? number_format((float)preg_replace('/[^0-9.]/', '', (string)$item->nilai_kontrak), 0, ',', '.') : '-',
                $item->kontraktor ?: '-',
                $statusBapLabel,
                $item->surveys->count(),
                $item->petugas->count()
            ]);
        }

        fclose($handle);
        exit;
    }

    public function cetak(Request $request)
    {
        $kegiatans = DataMingguan::with(['bap', 'petugas', 'surveys'])->latest()->get();
        $stats = [
            'total'  => $kegiatans->count(),
            'terbit' => $kegiatans->filter(fn($k) => $k->bap && $k->bap->status === 'terbit')->count(),
            'draft'  => $kegiatans->filter(fn($k) => $k->bap && $k->bap->status === 'draft')->count(),
            'belum'  => $kegiatans->filter(fn($k) => !$k->bap)->count(),
        ];

        return view('laporan.cetak', compact('kegiatans', 'stats'));
    }
}
