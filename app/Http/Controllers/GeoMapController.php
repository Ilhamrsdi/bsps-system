<?php

namespace App\Http\Controllers;

use App\Models\DataPenerima;
use Illuminate\Http\Request;

class GeoMapController extends Controller
{
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // Ambil semua penerima yang SUDAH memiliki koordinat GPS dari survei lapangan
        $query = DataPenerima::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', '')
            ->where('longitude', '!=', '');

        if ($user && $user->isAdminKecamatan()) {
            $query->where('kecamatan', $user->kecamatan);
        }

        $penerimaList = $query->with('petugas')
            ->orderBy('updated_at', 'desc')
            ->get();

        $markers = $penerimaList->map(function ($item) {
            // Hitung jumlah indikator RTLH yang terpenuhi
            $indikatorTerpenuhi = 0;
            if ($item->indikator_lantai === 'tidak_ada')    $indikatorTerpenuhi++;
            if ($item->indikator_pondasi === 'tidak_ada')   $indikatorTerpenuhi++;
            if ($item->indikator_dinding === 'tidak_ada')   $indikatorTerpenuhi++;
            if ($item->indikator_struktur === 'tidak_ada')  $indikatorTerpenuhi++;
            if ($item->indikator_atap === 'tidak_ada')      $indikatorTerpenuhi++;
            if ($item->indikator_penghasilan === 'ada')     $indikatorTerpenuhi++;

            $allIndicatorsFilled = !is_null($item->indikator_lantai)
                && !is_null($item->indikator_pondasi)
                && !is_null($item->indikator_dinding)
                && !is_null($item->indikator_struktur)
                && !is_null($item->indikator_atap)
                && !is_null($item->indikator_penghasilan);

            $hasAllPhotos = $item->foto_sudut_depan
                && $item->foto_sudut_belakang
                && $item->foto_bagian_dalam
                && $item->foto_sudut_kiri
                && $item->foto_sudut_kanan;

            // Tentukan warna & status berdasarkan kelengkapan survei
            if ($allIndicatorsFilled && $hasAllPhotos) {
                $color       = $indikatorTerpenuhi >= 2 ? 'green' : 'orange';
                $statusLabel = $indikatorTerpenuhi >= 2 ? '✅ Layak Diusulkan' : '⚠️ Tidak Layak';
                $status      = 'selesai';
            } else {
                $color       = 'purple';
                $statusLabel = '📋 Survei Belum Lengkap';
                $status      = 'survei';
            }

            $petugasNama = $item->petugas ? $item->petugas->name : 'Petugas Lapangan';

            $photoPath = $item->foto_sudut_depan ?: $item->ktp;
            $fotoUrl = null;
            if ($photoPath) {
                $cleanPath = ltrim($photoPath, '/');
                if (str_starts_with($cleanPath, 'http://') || str_starts_with($cleanPath, 'https://')) {
                    $fotoUrl = $cleanPath;
                } elseif (str_starts_with($cleanPath, 'uploads/') || str_starts_with($cleanPath, 'storage/')) {
                    $fotoUrl = asset($cleanPath);
                } else {
                    $fotoUrl = asset('uploads/' . $cleanPath);
                }
            }

            return [
                'id'          => 'penerima_' . $item->id,
                'type'        => 'survey',
                'name'        => $item->nama,
                'nik'         => $item->no_ktp ?: '-',
                'no_kk'       => $item->no_kk ?: '-',
                'location'    => 'Desa ' . ($item->desa_kelurahan ?: '-') . ', Kec. ' . ($item->kecamatan ?: '-'),
                'alamat'      => $item->alamat ?: '-',
                'lat'         => (float) $item->latitude,
                'lng'         => (float) $item->longitude,
                'status'      => $status,
                'statusLabel' => $statusLabel,
                'color'       => $color,
                'petugas'     => $petugasNama,
                'tanggal'     => $item->updated_at ? $item->updated_at->setTimezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'foto'        => $fotoUrl,
                'desil'       => $item->pengelompokan_desil ?: '-',
                'bap_id'      => $item->id,
            ];
        });

        return view('geoMaps.index', compact('markers'));
    }
}
