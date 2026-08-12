<?php

namespace App\Http\Controllers;

use App\Models\DataPenerima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeoMapController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Ambil penerima yang MEMILIKI koordinat GPS (Muncul di Peta)
        $queryGps = DataPenerima::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', '')
            ->where('longitude', '!=', '');

        if ($user && $user->isAdminKecamatan()) {
            $queryGps->where('kecamatan', $user->kecamatan);
        }

        $penerimaGpsList = $queryGps->with('petugas')
            ->orderBy('updated_at', 'desc')
            ->get();

        $markers = $penerimaGpsList->map(function ($item) {
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
                'penerima_id' => $item->id,
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

        // 2. Ambil penerima yang SUDAH SURVEI / VERVAL namun TIDAK MEMILIKI koordinat GPS (GPS Mati / Status Khusus)
        $queryNonGps = DataPenerima::sudahSurvei()
            ->where(function($q) {
                $q->whereNull('latitude')
                  ->orWhereNull('longitude')
                  ->orWhere('latitude', '')
                  ->orWhere('longitude', '');
            });

        if ($user && $user->isAdminKecamatan()) {
            $queryNonGps->where('kecamatan', $user->kecamatan);
        }

        $penerimaNonGpsList = $queryNonGps->with('petugas')
            ->orderBy('updated_at', 'desc')
            ->get();

        $nonGpsMarkers = $penerimaNonGpsList->map(function ($item) {
            $indikatorTerpenuhi = 0;
            if ($item->indikator_lantai === 'tidak_ada')    $indikatorTerpenuhi++;
            if ($item->indikator_pondasi === 'tidak_ada')   $indikatorTerpenuhi++;
            if ($item->indikator_dinding === 'tidak_ada')   $indikatorTerpenuhi++;
            if ($item->indikator_struktur === 'tidak_ada')  $indikatorTerpenuhi++;
            if ($item->indikator_atap === 'tidak_ada')      $indikatorTerpenuhi++;
            if ($item->indikator_penghasilan === 'ada')     $indikatorTerpenuhi++;

            $isStatusKhusus = in_array(strtolower($item->status), ['meninggal', 'pindah', 'tidak diketahui']);

            if ($isStatusKhusus) {
                $statusLabel = 'Khusus: ' . ucfirst($item->status);
                $badgeColor = 'purple';
                $keteranganGps = 'Status Khusus Lapangan';
            } elseif ($indikatorTerpenuhi >= 2) {
                $statusLabel = '✅ Layak Diusulkan (Tanpa GPS)';
                $badgeColor = 'green';
                $keteranganGps = 'GPS Tidak Aktif saat Survei';
            } else {
                $statusLabel = '⚠️ Tidak Layak (Tanpa GPS)';
                $badgeColor = 'orange';
                $keteranganGps = 'GPS Tidak Aktif saat Survei';
            }

            return [
                'id'            => $item->id,
                'nama'          => $item->nama,
                'nik'           => $item->no_ktp ?: '-',
                'no_kk'         => $item->no_kk ?: '-',
                'kecamatan'     => $item->kecamatan ?: '-',
                'desa'          => $item->desa_kelurahan ?: '-',
                'location'      => 'Desa ' . ($item->desa_kelurahan ?: '-') . ', Kec. ' . ($item->kecamatan ?: '-'),
                'alamat'        => $item->alamat ?: '-',
                'petugas'       => $item->petugas ? $item->petugas->name : 'Petugas Lapangan',
                'status'        => $item->status,
                'statusLabel'   => $statusLabel,
                'badgeColor'    => $badgeColor,
                'keteranganGps' => $keteranganGps,
                'tanggal'       => $item->updated_at ? $item->updated_at->setTimezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') . ' WIB' : '-',
            ];
        });

        $totalGps = $markers->count();
        $totalNonGps = $nonGpsMarkers->count();
        $totalSurvei = $totalGps + $totalNonGps;
        $countGpsMati = $nonGpsMarkers->where('keteranganGps', 'GPS Tidak Aktif saat Survei')->count();
        $countKhusus = $nonGpsMarkers->where('keteranganGps', 'Status Khusus Lapangan')->count();

        return view('geoMaps.index', compact(
            'markers',
            'nonGpsMarkers',
            'totalGps',
            'totalNonGps',
            'totalSurvei',
            'countGpsMati',
            'countKhusus'
        ));
    }
}
