<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\DataMingguan;
use App\Models\User;
use Illuminate\Http\Request;

class GeoMapController extends Controller
{
    public function index()
    {
        // 1. Ambil data survei kegiatan yang memiliki koordinat GPS
        $surveys = Survey::with('dataMingguan')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->latest('tanggal_survei')
            ->get();

        $surveyMarkers = $surveys->map(function ($s) {
            $status = 'survei';
            if ($s->dataMingguan) {
                $status = $s->dataMingguan->status;
            }
            $statusLabels = [
                'selesai'  => 'Selesai',
                'proses'   => 'Proses',
                'survei'   => 'Survei',
                'menunggu' => 'Menunggu',
                'batal'    => 'Batal'
            ];
            $statusColors = [
                'selesai'  => 'green',
                'proses'   => 'orange',
                'survei'   => 'purple',
                'menunggu' => 'blue',
                'batal'    => 'red'
            ];

            $foto = $s->foto_admin_1 ?: ($s->foto_fungsi_1 ?: ($s->foto_peruntukan_1 ?: null));

            return [
                'id'          => 'survey_' . $s->id,
                'type'        => 'survey',
                'name'        => $s->nama_kegiatan ?: ($s->dataMingguan->nama_kegiatan ?? 'Survei Lapangan'),
                'location'    => 'Kec. ' . ucwords(str_replace('_', ' ', $s->kecamatan ?: ($s->dataMingguan->lokasi ?? 'Jember'))),
                'alamat'      => $s->alamat_lokasi ?: ($s->dataMingguan->alamat ?? '-'),
                'lat'         => (float) $s->latitude,
                'lng'         => (float) $s->longitude,
                'status'      => $status,
                'statusLabel' => $statusLabels[$status] ?? 'Survei',
                'color'       => 'blue',
                'petugas'     => $s->nama_petugas_1 . ($s->nama_petugas_2 ? ' & ' . $s->nama_petugas_2 : ''),
                'tanggal'     => $s->tanggal_survei ? $s->tanggal_survei->format('d M Y') : '-',
                'foto'        => $foto ? asset($foto) : null,
                'bap_id'      => $s->data_mingguan_id,
            ];
        });

        // 2. Ambil data lokasi perangkat petugas aktif (live GPS dari HP petugas saat mulai survei)
        $petugasList = User::where('role', 'petugas')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->latest('last_location_at')
            ->get();

        $petugasMarkers = $petugasList->map(function ($p) {
            $deviceInfo = ($p->device_type ?? 'Perangkat HP') . ($p->last_ip ? ' (IP: ' . $p->last_ip . ')' : '');

            return [
                'id'          => 'petugas_' . $p->id,
                'type'        => 'petugas',
                'name'        => '📍 Lokasi Petugas: ' . $p->name,
                'location'    => 'Kec. ' . ucwords(str_replace('_', ' ', $p->kecamatan ?? 'Jember')),
                'alamat'      => $deviceInfo,
                'lat'         => (float) $p->latitude,
                'lng'         => (float) $p->longitude,
                'status'      => 'petugas_aktif',
                'statusLabel' => 'Petugas Aktif',
                'color'       => 'green',
                'petugas'     => $p->name . ' (' . ($p->jabatan ?? 'Petugas Lapangan') . ')',
                'tanggal'     => $p->last_location_at ? $p->last_location_at->diffForHumans() : 'Aktif',
                'foto'        => null,
                'bap_id'      => null,
            ];
        });

        // Gabungkan titik lokasi survei & lokasi live petugas
        $markers = $surveyMarkers->concat($petugasMarkers)->values();

        return view('geoMaps.index', compact('markers'));
    }
}
