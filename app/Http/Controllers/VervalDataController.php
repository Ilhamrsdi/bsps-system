<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class VervalDataController extends Controller
{
    /**
     * Tampilkan Halaman Daftar Data Verval BSPS
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'all');
        $kecamatan = $request->get('kecamatan', 'all');

        // Statistik Ringkasan Verval
        $stats = [
            'total'    => 24,
            'layak'    => 18,
            'proses'   => 4,
            'menunggu' => 2,
        ];

        // Master Dummy Data Calon Penerima Bantuan BSPS
        $allData = collect([
            (object)[
                'id' => 1,
                'no_berkas' => 'BSPS/2026/08/001',
                'nama_pemohon' => 'Bpk. Slamet Riyadi',
                'nik_pemohon' => '3509191204850001',
                'no_kk' => '3509191204050012',
                'lokasi' => 'Kaliwates',
                'desa' => 'Kel. Sempusari',
                'alamat' => 'Jl. Hayam Wuruk No. 45, RT 02/RW 05',
                'luas_rumah' => '36 m²',
                'status_tanah' => 'Hak Milik (SHM)',
                'skor_kelaikan' => '88 / 100',
                'rekomendasi' => 'Layak Bantuan (PK)',
                'status' => 'selesai',
                'tfl' => 'Ahmad Fauzi (TFL 01)',
                'tanggal_survei' => '08 Agt 2026',
            ],
            (object)[
                'id' => 2,
                'no_berkas' => 'BSPS/2026/08/002',
                'nama_pemohon' => 'Ibu Siti Aminah',
                'nik_pemohon' => '3509195508780003',
                'no_kk' => '3509195508050021',
                'lokasi' => 'Patrang',
                'desa' => 'Kel. Gebang',
                'alamat' => 'Lingkungan Gebang Timur, RT 01/RW 03',
                'luas_rumah' => '30 m²',
                'status_tanah' => 'Letter C Desa',
                'skor_kelaikan' => 'Proses Verifikasi',
                'rekomendasi' => 'Verifikasi Berkas',
                'status' => 'proses',
                'tfl' => 'Ahmad Fauzi (TFL 01)',
                'tanggal_survei' => '09 Agt 2026',
            ],
            (object)[
                'id' => 3,
                'no_berkas' => 'BSPS/2026/08/003',
                'nama_pemohon' => 'Bpk. Bambang Sutrisno',
                'nik_pemohon' => '3509191402750002',
                'no_kk' => '3509191402050033',
                'lokasi' => 'Sumbersari',
                'desa' => 'Kel. Antirogo',
                'alamat' => 'Dusun Antirogo Krajan, RT 03/RW 01',
                'luas_rumah' => '42 m²',
                'status_tanah' => 'Surat Hibah Desa',
                'skor_kelaikan' => 'Dalam Penilaian',
                'rekomendasi' => 'Survei Lapangan TFL',
                'status' => 'survei',
                'tfl' => 'Dwi Handoko (Koordinator)',
                'tanggal_survei' => '10 Agt 2026',
            ],
            (object)[
                'id' => 4,
                'no_berkas' => 'BSPS/2026/08/004',
                'nama_pemohon' => 'Ibu Nurul Hidayati',
                'nik_pemohon' => '3509194811800004',
                'no_kk' => '3509194811050045',
                'lokasi' => 'Rambipuji',
                'desa' => 'Desa Kaliwining',
                'alamat' => 'Dusun Krajan, RT 02/RW 02',
                'luas_rumah' => '36 m²',
                'status_tanah' => 'Hak Milik (SHM)',
                'skor_kelaikan' => '92 / 100',
                'rekomendasi' => 'Layak Bantuan (PK)',
                'status' => 'selesai',
                'tfl' => 'Budi Pratama (TFL 02)',
                'tanggal_survei' => '07 Agt 2026',
            ],
            (object)[
                'id' => 5,
                'no_berkas' => 'BSPS/2026/08/005',
                'nama_pemohon' => 'Bpk. Joko Santoso',
                'nik_pemohon' => '3509192106720005',
                'no_kk' => '3509192106050056',
                'lokasi' => 'Arjasa',
                'desa' => 'Desa Kemuning',
                'alamat' => 'Dusun Krajan, RT 04/RW 01',
                'luas_rumah' => '28 m²',
                'status_tanah' => 'Petok D',
                'skor_kelaikan' => 'Menunggu Penugasan',
                'rekomendasi' => 'Belum Disurvei',
                'status' => 'menunggu',
                'tfl' => 'Belum Ditugaskan',
                'tanggal_survei' => '-',
            ],
            (object)[
                'id' => 6,
                'no_berkas' => 'BSPS/2026/08/006',
                'nama_pemohon' => 'Bpk. Sunarto',
                'nik_pemohon' => '3509191703810006',
                'no_kk' => '3509191703050067',
                'lokasi' => 'Ajung',
                'desa' => 'Desa Klompangan',
                'alamat' => 'Dusun Krajan, RT 01/RW 04',
                'luas_rumah' => '36 m²',
                'status_tanah' => 'Hak Milik (SHM)',
                'skor_kelaikan' => '85 / 100',
                'rekomendasi' => 'Layak Bantuan (PK)',
                'status' => 'selesai',
                'tfl' => 'Budi Pratama (TFL 02)',
                'tanggal_survei' => '06 Agt 2026',
            ],
        ]);

        // Filter Logic
        $filtered = $allData->filter(function ($item) use ($search, $status, $kecamatan) {
            if ($search) {
                $matchSearch = str_contains(strtolower($item->nama_pemohon), strtolower($search))
                    || str_contains(strtolower($item->nik_pemohon), strtolower($search))
                    || str_contains(strtolower($item->no_berkas), strtolower($search))
                    || str_contains(strtolower($item->alamat), strtolower($search));
                if (!$matchSearch) return false;
            }

            if ($status !== 'all') {
                if ($item->status !== $status) return false;
            }

            if ($kecamatan !== 'all') {
                if (strtolower($item->lokasi) !== strtolower($kecamatan)) return false;
            }

            return true;
        });

        // Pagination
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentPageItems = $filtered->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $vervals = new LengthAwarePaginator($currentPageItems, count($filtered), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view('verval_data.index', compact('vervals', 'stats'));
    }
}
