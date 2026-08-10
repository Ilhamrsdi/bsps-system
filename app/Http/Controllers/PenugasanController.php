<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PenugasanController extends Controller
{
    /**
     * Tampilkan Halaman Penugasan Fasilitator Lapangan (TFL) BSPS Verval
     */
    public function index(Request $request)
    {
        // Dummy list petugas
        $allPetugas = collect([
            (object)['id' => 1, 'name' => 'Ahmad Fauzi', 'nip' => '19890412 201801 1 004', 'jabatan' => 'Tenaga Fasilitator Lapangan (TFL)'],
            (object)['id' => 2, 'name' => 'Budi Pratama', 'nip' => '19920815 202012 1 002', 'jabatan' => 'Tenaga Fasilitator Lapangan (TFL)'],
            (object)['id' => 3, 'name' => 'Dwi Handoko', 'nip' => '19850320 201402 1 001', 'jabatan' => 'Koordinator Fasilitator Lapangan'],
        ]);

        // Dummy data kegiatan usulan BSPS
        $rawKegiatans = collect([
            (object)[
                'id' => 1,
                'nama_kegiatan' => 'Verval Calon Penerima Bantuan BSPS - Bpk. Slamet Riyadi',
                'lokasi' => 'Kaliwates',
                'nama_pemohon' => 'Bpk. Slamet Riyadi',
                'alamat' => 'Jl. Hayam Wuruk No. 45, RT 02/RW 05, Kel. Sempusari',
                'status' => 'proses',
                'tanggal' => now()->subDays(2),
                'petugas' => collect([$allPetugas[0], $allPetugas[1]]),
            ],
            (object)[
                'id' => 2,
                'nama_kegiatan' => 'Verifikasi Lapangan RTLH - Ibu Siti Aminah',
                'lokasi' => 'Patrang',
                'nama_pemohon' => 'Ibu Siti Aminah',
                'alamat' => 'Lingkungan Gebang Timur, RT 01/RW 03, Kel. Gebang',
                'status' => 'survei',
                'tanggal' => now()->subDays(3),
                'petugas' => collect([$allPetugas[0]]),
            ],
            (object)[
                'id' => 3,
                'nama_kegiatan' => 'Verifikasi Validasi Rumah Swadaya - Bpk. Bambang Sutrisno',
                'lokasi' => 'Sumbersari',
                'nama_pemohon' => 'Bpk. Bambang Sutrisno',
                'alamat' => 'Dusun Antirogo Krajan, RT 03/RW 01, Kel. Antirogo',
                'status' => 'menunggu',
                'tanggal' => now()->subDays(4),
                'petugas' => collect([]),
            ],
            (object)[
                'id' => 4,
                'nama_kegiatan' => 'Survei Kelaikan Komponen Bangunan - Ibu Nurul Hidayati',
                'lokasi' => 'Rambipuji',
                'nama_pemohon' => 'Ibu Nurul Hidayati',
                'alamat' => 'Dusun Krajan, Desa Kaliwining',
                'status' => 'selesai',
                'tanggal' => now()->subDays(5),
                'petugas' => collect([$allPetugas[1], $allPetugas[2]]),
            ],
            (object)[
                'id' => 5,
                'nama_kegiatan' => 'Verifikasi Data Usulan BSPS - Bpk. Joko Santoso',
                'lokasi' => 'Arjasa',
                'nama_pemohon' => 'Bpk. Joko Santoso',
                'alamat' => 'Dusun Krajan, Desa Kemuning',
                'status' => 'menunggu',
                'tanggal' => now()->subDays(6),
                'petugas' => collect([]),
            ],
        ]);

        // Pagination Dummy
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        $currentPageItems = $rawKegiatans->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $kegiatans = new LengthAwarePaginator($currentPageItems, count($rawKegiatans), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        $stats = [
            'total_kegiatan'    => 5,
            'sudah_ditugaskan'  => 3,
            'belum_ditugaskan'  => 2,
            'total_petugas'     => 3,
        ];

        return view('penugasan.index', compact('kegiatans', 'allPetugas', 'stats'));
    }

    /**
     * Simpan / Perbarui Penugasan (Dummy handler)
     */
    public function update(Request $request, $id = null)
    {
        return redirect()->back()->with('success', 'Penugasan Tenaga Fasilitator Lapangan (TFL) berhasil diperbarui!');
    }
}
