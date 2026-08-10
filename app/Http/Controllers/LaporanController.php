<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'progress');
        $search = $request->get('search');
        $kecamatan = $request->get('kecamatan');

        $stats = [
            'total_kegiatan' => 24,
            'bap_terbit'     => 18,
            'belum_bap'      => 6,
            'survei_selesai' => 18,
        ];

        // Dummy Data Rekapitulasi Verval BSPS
        $rawKegiatans = collect([
            (object)[
                'id' => 1,
                'nama_kegiatan' => 'Verval Calon Penerima Bantuan BSPS - Bpk. Slamet Riyadi',
                'lokasi' => 'Kaliwates',
                'alamat' => 'Jl. Hayam Wuruk No. 45, Kel. Sempusari',
                'nama_pemohon' => 'Bpk. Slamet Riyadi',
                'status' => 'selesai',
                'tanggal' => now()->subDays(2),
                'bap' => (object)['nomor_bap' => 'BAP/BSPS/2026/001', 'status' => 'terbit'],
                'surveys' => collect([(object)['id' => 101]]),
                'petugas' => collect([(object)['name' => 'Ahmad Fauzi']]),
            ],
            (object)[
                'id' => 2,
                'nama_kegiatan' => 'Verifikasi Lapangan RTLH - Ibu Siti Aminah',
                'lokasi' => 'Patrang',
                'alamat' => 'Lingkungan Gebang Timur, Kel. Gebang',
                'nama_pemohon' => 'Ibu Siti Aminah',
                'status' => 'proses',
                'tanggal' => now()->subDays(3),
                'bap' => null,
                'surveys' => collect([]),
                'petugas' => collect([(object)['name' => 'Ahmad Fauzi']]),
            ],
            (object)[
                'id' => 3,
                'nama_kegiatan' => 'Verifikasi Validasi Rumah Swadaya - Bpk. Bambang Sutrisno',
                'lokasi' => 'Sumbersari',
                'alamat' => 'Dusun Antirogo Krajan, Kel. Antirogo',
                'nama_pemohon' => 'Bpk. Bambang Sutrisno',
                'status' => 'survei',
                'tanggal' => now()->subDays(4),
                'bap' => null,
                'surveys' => collect([]),
                'petugas' => collect([]),
            ],
            (object)[
                'id' => 4,
                'nama_kegiatan' => 'Survei Kelaikan Komponen Bangunan - Ibu Nurul Hidayati',
                'lokasi' => 'Rambipuji',
                'alamat' => 'Dusun Krajan, Desa Kaliwining',
                'nama_pemohon' => 'Ibu Nurul Hidayati',
                'status' => 'selesai',
                'tanggal' => now()->subDays(5),
                'bap' => (object)['nomor_bap' => 'BAP/BSPS/2026/002', 'status' => 'terbit'],
                'surveys' => collect([(object)['id' => 102]]),
                'petugas' => collect([(object)['name' => 'Budi Pratama']]),
            ],
            (object)[
                'id' => 5,
                'nama_kegiatan' => 'Verifikasi Data Usulan BSPS - Bpk. Joko Santoso',
                'lokasi' => 'Arjasa',
                'alamat' => 'Dusun Krajan, Desa Kemuning',
                'nama_pemohon' => 'Bpk. Joko Santoso',
                'status' => 'menunggu',
                'tanggal' => now()->subDays(6),
                'bap' => null,
                'surveys' => collect([]),
                'petugas' => collect([]),
            ],
        ]);

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        $currentPageItems = $rawKegiatans->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $kegiatans = new LengthAwarePaginator($currentPageItems, count($rawKegiatans), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        $surveylist = $kegiatans;
        $baplist = $kegiatans;
        $petugaslist = $kegiatans;

        return view('laporan.index', compact('stats', 'kegiatans', 'surveylist', 'baplist', 'petugaslist'));
    }

    public function cetak(Request $request)
    {
        return view('laporan.cetak');
    }

    public function exportExcel(Request $request)
    {
        return redirect()->back()->with('info', 'Fitur Export Laporan Excel telah disiapkan.');
    }
}
