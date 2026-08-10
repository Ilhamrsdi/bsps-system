<?php

namespace Database\Seeders;

use App\Models\DataMingguan;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DataMingguanSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $kecamatans = [
            'kaliwates', 'sumbersari', 'patrang', 'ajung', 'rambipuji',
            'balung', 'ambulu', 'wuluhan', 'puger', 'kencong',
            'gumukmas', 'umbulsari', 'semboro', 'jombang', 'silo',
            'mayang', 'mumbulsari', 'jenggawah', 'tempurejo', 'pakusari',
            'sukowono', 'kalisat', 'ledokombo', 'sumberjambe', 'arjasa',
            'jelbuk', 'bangsalsari', 'panti', 'sukorambi', 'tanggul', 'sumberbaru',
        ];

        $statuses = ['proses', 'selesai', 'menunggu', 'survei', 'batal'];

        $namaKegiatanList = [
            'Perbaikan Jalan Lingkungan Permukiman',
            'Pembangunan Drainase Pemukiman Warga',
            'Rehabilitasi Jembatan Desa & Gorong-Gorong',
            'Peningkatan Jalan Kabupaten Ruas Utama',
            'Pembangunan Talud Penahan Tanah Longsor',
            'Normalisasi Saluran Irigasi Pertanian',
            'Perbaikan Gorong-Gorong & Saluran Air',
            'Pembangunan Jalan Usaha Tani (JUT)',
            'Pemasangan Penerangan Jalan Umum (PJU)',
            'Pembangunan Trotoar Perkotaan & Pedestrian',
            'Rehabilitasi Gedung Balai Desa',
            'Pembangunan Embung Penampung Air Pertanian',
            'Perbaikan Tanggul Sungai Rawan Banjir',
            'Peningkatan Jalan Poros Desa Antar Dusun',
            'Pembangunan Jembatan Gantung Penyeberangan',
            'Normalisasi Sungai Bedadung Sektor Perkotaan',
            'Perbaikan Saluran Pembuang Limbah Permukiman',
            'Pembangunan Bak Penampung Air Bersih',
            'Peningkatan Drainase Perkotaan Jember',
            'Rehabilitasi Bendung Irigasi Teknis',
            'Pembangunan Jalan Beton Desa (Rabat Beton)',
            'Perbaikan Jembatan Beton Penghubung Kecamatan',
            'Pemasangan Box Culvert Saluran Utama',
            'Pembangunan Turap Sungai Penahan Abrasi',
            'Normalisasi Saluran Sekunder Irigasi',
            'Perbaikan Jalan Aspal Hotmix Kabupaten',
            'Pembangunan Pintu Air Irigasi Sawah',
            'Peningkatan Jalan Lingkar Desa',
            'Rehabilitasi Saluran Tersier Kelompok Tani',
            'Pembangunan Jalan Produksi Perkebunan',
            'Perbaikan Drainase Kompleks Pasar Tradisional',
            'Pembangunan Jembatan Rangka Baja',
            'Normalisasi Aliran Kali Jompo',
            'Peningkatan Akses Jalan Wisata Daerah',
            'Pembangunan Talud Bronjong Sungai',
            'Perbaikan Jalan Perbatasan Antar Kabupaten',
            'Rehabilitasi Gedung Puskesmas Pembantu',
            'Pembangunan Saluran Induk SDA',
            'Pemasangan Paving Block Jalan Gang Warga',
            'Perbaikan Jembatan Kayu Antar Dusun',
            'Normalisasi Saluran Irigasi Primer',
            'Pembangunan Jalan Evakuasi Bencana',
            'Peningkatan Jalan Antar Desa',
            'Rehabilitasi Bendungan Kecil (Embung)',
            'Pembangunan Drainase Samping Jalan Raya',
            'Perbaikan Gorong-Gorong Beton Besar',
            'Pemasangan Guardrail Pengaman Jalan Rawan',
            'Pembangunan Jalan Setapak Lingkungan',
            'Normalisasi Sungai Kali Mayang',
            'Perbaikan Saluran Irigasi Teknis Sawah',
        ];

        $pemohonList = [
            'H. Ahmad Subagyo (Kades Kaliwates)',
            'Drs. Bambang Hariyanto, M.Si',
            'Kelompok Tani Tani Makmur',
            'Poktan Sido Mulyo',
            'Hj. Siti Aminah, S.Pd',
            'Ir. Eko Prasetyo',
            'KSPPS Bintang Utama Jember',
            'RW 05 Kelurahan Kebonsari',
            'Pengurus HIPPA Tirta Jaya',
            'LSM Peduli Infrastruktur Jember',
            'Pemerintah Desa Sumbersari',
            'Dinas Pertanian Kab. Jember',
        ];

        $jalanList = [
            'Jl. Gajah Mada No. ',
            'Jl. Hayam Wuruk No. ',
            'Jl. Ahmad Yani No. ',
            'Jl. Kalimantan No. ',
            'Jl. Mastrip No. ',
            'Jl. Trunojoyo No. ',
            'Jl. Sunan Giri No. ',
            'Jl. PB Sudirman No. ',
            'Jl. R.A. Kartini No. ',
            'Dusun Krajan RT ',
        ];

        $kontraktorList = [
            'CV. Maju Jaya Konstruksi',
            'PT. Bangun Nusantara Jember',
            'CV. Karya Mandiri Utama',
            'PT. Cipta Graha Indonesia',
            'CV. Sumber Rejeki Teknik',
            'PT. Jaya Abadi Konstruksi',
            'CV. Berkah Bersama Jember',
            'PT. Graha Teknik Nusantara',
            'CV. Putra Jember Mandiri',
            'PT. Bumi Konstruksi Perkasa',
            null, null,
        ];

        $deskripsiList = [
            'Pekerjaan berjalan sesuai jadwal. Cuaca mendukung pelaksanaan di lapangan.',
            'Terdapat kendala pengiriman material dari supplier. Pengawasan ekstra diterapkan.',
            'Koordinasi dengan tokoh masyarakat dan warga setempat berjalan lancar.',
            'Pekerjaan fisik telah selesai 100%. Tinggal tahap pembersihan area lokasi.',
            'Menunggu klarifikasi revisi gambar kerja dari konsultan perencana.',
            'Intensitas hujan tinggi sempat memperlambat pengerjaan fisik selama 2 hari.',
            'Material dan alat berat sudah disiapkan di lokasi kegiatan.',
            'Pekerjaan dihentikan sementara untuk penyesuaian patok batas lahan.',
            null, null,
        ];

        $data = [];

        for ($i = 0; $i < 50; $i++) {
            $status   = $statuses[array_rand($statuses)];
            $kecamatan = $kecamatans[array_rand($kecamatans)];

            $daysAgo  = rand(0, 90);
            $tanggal  = now()->subDays($daysAgo)->format('Y-m-d');
            $minggu   = (int) date('W', strtotime($tanggal));

            $nilaiRaw     = rand(0, 1) ? rand(50, 5000) * 1000000 : null;
            $nilaiKontrak = $nilaiRaw ? number_format($nilaiRaw, 0, ',', '.') : null;

            $namaPemohon  = $pemohonList[array_rand($pemohonList)];
            $alamat       = $jalanList[array_rand($jalanList)] . rand(1, 150) . ', Kec. ' . ucfirst($kecamatan) . ', Jember';

            $data[] = [
                'nama_kegiatan' => $namaKegiatanList[$i % count($namaKegiatanList)],
                'nama_pemohon'  => $namaPemohon,
                'lokasi'        => $kecamatan,
                'alamat'        => $alamat,
                'tanggal'       => $tanggal,
                'minggu'        => $minggu,
                'status'        => $status,
                'status_bap'    => 'belum',
                'nilai_kontrak' => $nilaiKontrak,
                'kontraktor'    => $kontraktorList[array_rand($kontraktorList)],
                'deskripsi'     => $deskripsiList[array_rand($deskripsiList)],
                'user_id'       => 1,
                'created_at'    => now()->subDays($daysAgo),
                'updated_at'    => now()->subDays(rand(0, $daysAgo)),
            ];
        }

        DataMingguan::insert($data);

        $this->command->info('50 data kegiatan (seluruhnya status_bap = belum) berhasil di-seed!');
    }
}
