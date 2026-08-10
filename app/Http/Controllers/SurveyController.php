<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurveyController extends Controller
{
    /**
     * Tampilkan Form Survei Lapangan BSPS Verval
     */
    public function index(Request $request)
    {
        // Data Dummy Calon Penerima / Kegiatan Survei
        $kegiatans = collect([
            (object)[
                'id' => 1,
                'nama_kegiatan' => 'Verval Calon Penerima Bantuan BSPS - Bpk. Slamet Riyadi',
                'lokasi' => 'Kaliwates',
                'nama_pemohon' => 'Bpk. Slamet Riyadi',
                'nik_pemohon' => '3509191204850001',
                'alamat' => 'Jl. Hayam Wuruk No. 45, RT 02/RW 05, Kel. Sempusari',
                'petugas' => collect([
                    (object)['name' => 'Ahmad Fauzi (TFL BSPS)'],
                    (object)['name' => 'Budi Pratama (Fasilitator)']
                ]),
            ],
            (object)[
                'id' => 2,
                'nama_kegiatan' => 'Verifikasi Lapangan RTLH - Ibu Siti Aminah',
                'lokasi' => 'Patrang',
                'nama_pemohon' => 'Ibu Siti Aminah',
                'nik_pemohon' => '3509195508780003',
                'alamat' => 'Lingkungan Gebang Timur, RT 01/RW 03, Kel. Gebang',
                'petugas' => collect([
                    (object)['name' => 'Ahmad Fauzi (TFL BSPS)']
                ]),
            ],
            (object)[
                'id' => 3,
                'nama_kegiatan' => 'Verifikasi Validasi Rumah Swadaya - Bpk. Bambang Sutrisno',
                'lokasi' => 'Sumbersari',
                'nama_pemohon' => 'Bpk. Bambang Sutrisno',
                'nik_pemohon' => '3509191402750002',
                'alamat' => 'Dusun Antirogo Krajan, RT 03/RW 01, Kel. Antirogo',
                'petugas' => collect([
                    (object)['name' => 'Dwi Handoko (Koordinator)']
                ]),
            ],
        ]);

        $selectedKegiatan = $kegiatans->firstWhere('id', $request->get('kegiatan_id', 1)) ?? $kegiatans->first();

        // Data dummy survei eksisting (contoh hasil input lapangan)
        $existingSurvey = (object)[
            'id' => 101,
            'data_mingguan_id' => $selectedKegiatan->id,
            'nama_kegiatan' => $selectedKegiatan->nama_kegiatan,
            'nama_petugas_1' => 'Ahmad Fauzi (TFL BSPS)',
            'nama_petugas_2' => 'Budi Pratama (Fasilitator)',
            'tanggal_survei' => now()->subDays(1),
            'nama_pemohon' => $selectedKegiatan->nama_pemohon,
            'nik_pemohon' => $selectedKegiatan->nik_pemohon,
            'no_kk' => '3509191204050012',
            'jumlah_jiwa' => 4,
            'penghasilan' => 'Rp 1.500.000 / bulan (MBR)',
            'alamat_pemohon' => $selectedKegiatan->alamat,
            'jenis_bangunan' => 'Rumah Swadaya (RTLH)',
            'fungsi_bangunan' => 'Fungsi Hunian',
            'jumlah_lantai' => 1,
            'tinggi_bangunan' => 3.2,
            'luas_bangunan' => 36,
            'luas_tanah' => 72,
            'status_hak_tanah' => 'Hak Milik (Surat Keterangan Desa)',
            'kecamatan' => $selectedKegiatan->lokasi,
            'desa_kelurahan' => 'Sempusari',
            'nama_jalan' => 'Jl. Hayam Wuruk Gg. Mawar No. 12',
            'alamat_lokasi' => $selectedKegiatan->alamat,
            'latitude' => '-8.1721',
            'longitude' => '113.6997',
            'kondisi_pondasi' => 'Rusak Sedang',
            'kondisi_kolom' => 'Kayu Lapuk / Tanpa Balok',
            'kondisi_atap' => 'Bocor & Sebagian Rusak',
            'kondisi_dinding' => 'Bambu / Papan Sebagian Jebol',
            'kondisi_lantai' => 'Tanah Sebagian Plester',
            'kondisi_sanitasi' => 'Belum Memiliki Jamban Sehat',
            'rekomendasi' => 'Layak Bantuan BSPS (Peningkatan Kualitas)',
            'catatan_petugas' => 'Kondisi bangunan tidak memenuhi standar keselamatan dan kesehatan hunian. Sangat direkomendasikan menerima Bantuan Stimulan Perumahan Swadaya.',
            'item_admin' => 'sesuai',
            'item_tata' => 'sesuai',
            'item_fungsi' => 'sesuai',
            'item_peruntukan' => 'sesuai',
            'item_kelaikan' => 'sesuai',
            'kesimpulan_akhir' => 'Layak Mendapatkan Bantuan BSPS',
            'catatan_admin' => 'Dokumen kependudukan dan kepemilikan tanah lengkap serta sah.',
        ];

        return view('survey.index', compact('kegiatans', 'selectedKegiatan', 'existingSurvey'));
    }

    /**
     * Simpan / Perbarui Hasil Survei Lapangan (Dummy handler)
     */
    public function store(Request $request)
    {
        return redirect()->route('survey', ['kegiatan_id' => $request->get('data_mingguan_id', 1)])
            ->with('success', 'Data Survei Verval Lapangan BSPS berhasil disimpan!');
    }

    /**
     * Upload Foto Lapangan
     */
    public function uploadPhoto(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Foto dokumentasi lapangan berhasil diunggah (Dummy Mode).',
            'file_url' => asset('logo.jpg')
        ]);
    }
}
