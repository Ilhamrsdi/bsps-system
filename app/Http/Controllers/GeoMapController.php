<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeoMapController extends Controller
{
    public function index()
    {
        // Data Dummy Titik Sebaran Calon Penerima Bantuan BSPS & RTLH
        $markers = collect([
            [
                'id'          => 'survey_1',
                'type'        => 'survey',
                'name'        => 'Verval Calon Penerima BSPS - Bpk. Slamet Riyadi',
                'location'    => 'Kec. Kaliwates',
                'alamat'      => 'Jl. Hayam Wuruk No. 45, RT 02/RW 05, Kel. Sempusari',
                'lat'         => -8.1721,
                'lng'         => 113.6997,
                'status'      => 'selesai',
                'statusLabel' => 'Layak Bantuan (PK)',
                'color'       => 'green',
                'petugas'     => 'Ahmad Fauzi (TFL BSPS)',
                'tanggal'     => '10 Agt 2026',
                'foto'        => asset('logo.jpg'),
                'bap_id'      => null,
            ],
            [
                'id'          => 'survey_2',
                'type'        => 'survey',
                'name'        => 'Verifikasi Lapangan RTLH - Ibu Siti Aminah',
                'location'    => 'Kec. Patrang',
                'alamat'      => 'Lingkungan Gebang Timur, RT 01/RW 03, Kel. Gebang',
                'lat'         => -8.1512,
                'lng'         => 113.7125,
                'status'      => 'proses',
                'statusLabel' => 'Proses Verifikasi',
                'color'       => 'orange',
                'petugas'     => 'Ahmad Fauzi (TFL BSPS)',
                'tanggal'     => '09 Agt 2026',
                'foto'        => asset('logo.jpg'),
                'bap_id'      => null,
            ],
            [
                'id'          => 'survey_3',
                'type'        => 'survey',
                'name'        => 'Verifikasi Validasi Rumah Swadaya - Bpk. Bambang Sutrisno',
                'location'    => 'Kec. Sumbersari',
                'alamat'      => 'Dusun Antirogo Krajan, RT 03/RW 01, Kel. Antirogo',
                'lat'         => -8.1634,
                'lng'         => 113.7258,
                'status'      => 'survei',
                'statusLabel' => 'Survei Lapangan',
                'color'       => 'purple',
                'petugas'     => 'Dwi Handoko (Koordinator)',
                'tanggal'     => '08 Agt 2026',
                'foto'        => asset('logo.jpg'),
                'bap_id'      => null,
            ],
            [
                'id'          => 'survey_4',
                'type'        => 'survey',
                'name'        => 'Survei Kelaikan Bangunan - Ibu Nurul Hidayati',
                'location'    => 'Kec. Rambipuji',
                'alamat'      => 'Dusun Krajan, Desa Kaliwining',
                'lat'         => -8.2045,
                'lng'         => 113.6087,
                'status'      => 'selesai',
                'statusLabel' => 'Layak Bantuan (PK)',
                'color'       => 'green',
                'petugas'     => 'Budi Pratama (Fasilitator)',
                'tanggal'     => '07 Agt 2026',
                'foto'        => asset('logo.jpg'),
                'bap_id'      => null,
            ],
            [
                'id'          => 'petugas_1',
                'type'        => 'petugas',
                'name'        => '📍 Posisi TFL: Ahmad Fauzi',
                'location'    => 'Kec. Kaliwates',
                'alamat'      => 'Perangkat HP Android (GPS Aktif)',
                'lat'         => -8.1750,
                'lng'         => 113.6920,
                'status'      => 'petugas_aktif',
                'statusLabel' => 'TFL Bertugas',
                'color'       => 'blue',
                'petugas'     => 'Ahmad Fauzi (TFL BSPS)',
                'tanggal'     => 'Aktif Sekarang',
                'foto'        => null,
                'bap_id'      => null,
            ],
        ]);

        return view('geoMaps.index', compact('markers'));
    }
}
