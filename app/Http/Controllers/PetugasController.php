<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasController extends Controller
{
    /**
     * Dashboard Khusus Petugas / Fasilitator Lapangan
     */
    public function dashboard()
    {
        $user = Auth::user() ?? (object)['name' => 'Ahmad Fauzi', 'jabatan' => 'Tenaga Fasilitator Lapangan'];
        
        $stats = [
            'total_tugas'   => 8,
            'sudah_survei'  => 6,
            'belum_survei'  => 2,
        ];

        $kegiatans = collect([
            (object)[
                'id' => 1,
                'nama_kegiatan' => 'Verval Calon Penerima Bantuan BSPS - Bpk. Slamet Riyadi',
                'lokasi' => 'Kaliwates',
                'alamat' => 'Jl. Hayam Wuruk No. 45, Kel. Sempusari',
                'status_survei' => 'selesai',
                'tanggal' => now()->subDays(1),
            ],
            (object)[
                'id' => 2,
                'nama_kegiatan' => 'Verifikasi Lapangan RTLH - Ibu Siti Aminah',
                'lokasi' => 'Patrang',
                'alamat' => 'Lingkungan Gebang Timur, Kel. Gebang',
                'status_survei' => 'belum',
                'tanggal' => now()->subDays(2),
            ],
        ]);

        return view('petugas.dashboard', compact('user', 'stats', 'kegiatans'));
    }

    /**
     * Halaman Tugas Belum Survei
     */
    public function belumSurvei()
    {
        $kegiatans = collect([
            (object)[
                'id' => 2,
                'nama_kegiatan' => 'Verifikasi Lapangan RTLH - Ibu Siti Aminah',
                'lokasi' => 'Patrang',
                'nama_pemohon' => 'Ibu Siti Aminah',
                'alamat' => 'Lingkungan Gebang Timur, RT 01/RW 03, Kel. Gebang',
                'tanggal' => now()->subDays(2),
            ],
            (object)[
                'id' => 5,
                'nama_kegiatan' => 'Verifikasi Data Usulan BSPS - Bpk. Joko Santoso',
                'lokasi' => 'Arjasa',
                'nama_pemohon' => 'Bpk. Joko Santoso',
                'alamat' => 'Dusun Krajan, Desa Kemuning',
                'tanggal' => now()->subDays(4),
            ],
        ]);

        return view('petugas.belum_survei', compact('kegiatans'));
    }

    /**
     * Halaman Tugas Sudah Survei
     */
    public function sudahSurvei()
    {
        $kegiatans = collect([
            (object)[
                'id' => 1,
                'nama_kegiatan' => 'Verval Calon Penerima Bantuan BSPS - Bpk. Slamet Riyadi',
                'lokasi' => 'Kaliwates',
                'nama_pemohon' => 'Bpk. Slamet Riyadi',
                'alamat' => 'Jl. Hayam Wuruk No. 45, Kel. Sempusari',
                'tanggal_survei' => now()->subDays(1),
                'status_rekomendasi' => 'Layak Bantuan (PK)',
            ],
            (object)[
                'id' => 4,
                'nama_kegiatan' => 'Survei Kelaikan Komponen Bangunan - Ibu Nurul Hidayati',
                'lokasi' => 'Rambipuji',
                'nama_pemohon' => 'Ibu Nurul Hidayati',
                'alamat' => 'Dusun Krajan, Desa Kaliwining',
                'tanggal_survei' => now()->subDays(3),
                'status_rekomendasi' => 'Layak Bantuan (PK)',
            ],
        ]);

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
