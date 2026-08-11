<?php

namespace App\Http\Controllers;

use App\Models\DataPenerima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PencocokanDataController extends Controller
{
    /**
     * Tampilkan Halaman UI Pencocokan Data (Data Penerima BSPS vs Dataguse Kependudukan)
     */
    public function index(Request $request)
    {
        $search = trim($request->get('search', ''));
        $statusFilter = $request->get('status', 'all'); // all, cocok, beda, tidak_ditemukan

        $user = \Illuminate\Support\Facades\Auth::user();
        $query = DataPenerima::query();

        if ($user && $user->isAdminKecamatan()) {
            $query->where('kecamatan', $user->kecamatan);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_ktp', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('desa_kelurahan', 'like', "%{$search}%")
                  ->orWhere('kecamatan', 'like', "%{$search}%");
            });
        }

        // Ambil data lokal paginated
        $penerimas = $query->orderBy('id', 'asc')->paginate(15)->withQueryString();

        // Kumpulkan semua NIK dari item halaman ini untuk di-lookup batch ke Dataguse
        $niks = $penerimas->pluck('no_ktp')->map(fn($v) => trim($v))->filter()->unique()->values()->toArray();

        $pendudukMap = [];
        $dataguseConnected = false;

        if (!empty($niks)) {
            try {
                $rows = DB::connection('dataguse')
                    ->table('data_penduduks')
                    ->whereIn('nomor_induk_kependudukan', $niks)
                    ->get();

                foreach ($rows as $row) {
                    $nikKey = trim($row->nomor_induk_kependudukan ?? '');
                    if ($nikKey) {
                        $pendudukMap[$nikKey] = $row;
                    }
                }
                $dataguseConnected = true;
            } catch (\Exception $e) {
                Log::warning('Dataguse connection failed in PencocokanDataController: ' . $e->getMessage());
                $dataguseConnected = false;
            }
        }

        // Hitung status pencocokan per baris & statistik ringkas
        $totalCocok = 0;
        $totalBeda = 0;
        $totalTidakDitemukan = 0;

        $penerimas->transform(function ($item) use ($pendudukMap, &$totalCocok, &$totalBeda, &$totalTidakDitemukan) {
            $nik = trim($item->no_ktp);
            $dp = $pendudukMap[$nik] ?? null;

            if (!$dp) {
                $item->dg_status = 'tidak_ditemukan';
                $item->dg_data = null;
                $item->dg_diffs = [];
                $totalTidakDitemukan++;
                return $item;
            }

            // Ekstrak atribut Dataguse dengan fallback yang fleksibel
            $dgNama    = trim($dp->nama ?? '');
            $dgAlamat  = trim($dp->alamat ?? '');
            $dgDusun   = trim($dp->dusun ?? $dp->dukuh ?? $dp->kampung ?? '');
            $dgDesa    = trim($dp->desa_kelurahan ?? $dp->kelurahan ?? $dp->desa ?? '');
            $dgKec     = trim($dp->kecamatan ?? '');
            $dgKab     = trim($dp->kabupaten ?? $dp->kabupaten_kota ?? '');
            $dgRt      = trim($dp->rt ?? '');
            $dgRw      = trim($dp->rw ?? '');
            $dgRtRw    = (!empty($dgRt) || !empty($dgRw)) ? "RT {$dgRt} / RW {$dgRw}" : trim($dp->rt_rw ?? '');
            $dgNoKk    = trim($dp->nomor_kartu_keluarga ?? $dp->no_kk ?? '');

            $dgObj = (object) [
                'nik'            => $dp->nomor_induk_kependudukan ?? $item->no_ktp,
                'nama'           => $dgNama,
                'alamat'         => $dgAlamat,
                'dusun'          => $dgDusun,
                'desa_kelurahan' => $dgDesa,
                'kecamatan'      => $dgKec,
                'kabupaten_kota' => $dgKab,
                'rt_rw'          => $dgRtRw,
                'no_kk'          => $dgNoKk,
                'tempat_lahir'   => $dp->tempat_lahir ?? '',
                'tanggal_lahir'  => $dp->tanggal_lahir ?? '',
                'pekerjaan'      => $dp->pekerjaan ?? '',
                'jenis_kelamin'  => $dp->jenis_kelamin ?? '',
            ];

            // Cek perbedaan (cerdas & fleksibel)
            $diffs = [];
            
            // 1. Nama
            if ($dgNama && $this->cleanStr($item->nama) !== $this->cleanStr($dgNama)) {
                $diffs['nama'] = true;
            }
            
            // 2. Alamat (mengabaikan suffix RT/RW jika basis alamat cocok)
            if ($dgAlamat) {
                $cleanLokalAlamat = $this->cleanAlamat($item->alamat);
                $cleanDgAlamat    = $this->cleanAlamat($dgAlamat);
                if ($cleanLokalAlamat !== $cleanDgAlamat && !str_contains($cleanDgAlamat, $cleanLokalAlamat) && !str_contains($cleanLokalAlamat, $cleanDgAlamat)) {
                    $diffs['alamat'] = true;
                }
            }
            
            // 3. Dusun (jika lokal kosong tetapi alamat lokal mengandung nama dusun Dataguse, jangan anggap beda)
            if ($dgDusun) {
                $cleanLokalDusun  = $this->cleanStr($item->dusun);
                $cleanDgDusun     = $this->cleanStr($dgDusun);
                $cleanLokalAlamat = $this->cleanStr($item->alamat);
                if ($cleanLokalDusun !== $cleanDgDusun && !str_contains($cleanLokalAlamat, $cleanDgDusun)) {
                    $diffs['dusun'] = true;
                }
            }
            
            // 4. Desa / Kelurahan
            if ($dgDesa && $this->cleanStr($item->desa_kelurahan) !== $this->cleanStr($dgDesa)) {
                $diffs['desa_kelurahan'] = true;
            }
            
            // 5. Kecamatan
            if ($dgKec && $this->cleanStr($item->kecamatan) !== $this->cleanStr($dgKec)) {
                $diffs['kecamatan'] = true;
            }
            
            // 6. Kabupaten / Kota (mengabaikan prefiks "KAB." vs "Kabupaten")
            if ($dgKab && $this->cleanKab($item->kabupaten_kota) !== $this->cleanKab($dgKab)) {
                $diffs['kabupaten_kota'] = true;
            }

            $item->dg_data = $dgObj;
            $item->dg_diffs = $diffs;

            if (empty($diffs)) {
                $item->dg_status = 'cocok';
                $totalCocok++;
            } else {
                $item->dg_status = 'beda';
                $totalBeda++;
            }

            return $item;
        });

        // Filter koleksi berdasarkan status match jika user memilih saringan
        if ($statusFilter !== 'all') {
            $filteredItems = $penerimas->getCollection()->filter(function ($item) use ($statusFilter) {
                return $item->dg_status === $statusFilter;
            })->values();
            $penerimas->setCollection($filteredItems);
        }

        $stats = [
            'total'           => $penerimas->total(),
            'cocok'           => $totalCocok,
            'beda'            => $totalBeda,
            'tidak_ditemukan' => $totalTidakDitemukan,
        ];

        return view('pencocokan_data.index', compact('penerimas', 'stats', 'search', 'statusFilter', 'dataguseConnected'));
    }

    /**
     * Terapkan / Update Data Penerima Lokal dari Data Dataguse (Per Baris via Ajax)
     */
    public function syncSingle(Request $request, $id)
    {
        $penerima = DataPenerima::findOrFail($id);
        $nik = trim($penerima->no_ktp);

        if (!$nik) {
            return response()->json([
                'success' => false,
                'message' => 'Data penerima tidak memiliki NIK untuk dicocokkan.'
            ], 400);
        }

        try {
            $dp = DB::connection('dataguse')
                ->table('data_penduduks')
                ->where('nomor_induk_kependudukan', $nik)
                ->first();

            if (!$dp) {
                return response()->json([
                    'success' => false,
                    'message' => 'NIK ' . $nik . ' tidak ditemukan di database Dataguse.'
                ], 404);
            }

            $updateData = [];

            if (!empty($dp->nama)) {
                $updateData['nama'] = $dp->nama;
            }
            if (!empty($dp->alamat)) {
                $updateData['alamat'] = $dp->alamat;
            }
            $dgDusun = $dp->dusun ?? $dp->dukuh ?? $dp->kampung ?? null;
            if (!empty($dgDusun)) {
                $updateData['dusun'] = $dgDusun;
            }
            $dgDesa = $dp->desa_kelurahan ?? $dp->kelurahan ?? $dp->desa ?? null;
            if (!empty($dgDesa)) {
                $updateData['desa_kelurahan'] = $dgDesa;
            }
            if (!empty($dp->kecamatan)) {
                $updateData['kecamatan'] = $dp->kecamatan;
            }
            $dgKab = $dp->kabupaten ?? $dp->kabupaten_kota ?? null;
            if (!empty($dgKab)) {
                $updateData['kabupaten_kota'] = $dgKab;
            }
            if (!empty($dp->nomor_kartu_keluarga)) {
                $updateData['no_kk'] = $dp->nomor_kartu_keluarga;
            }
            if (!empty($dp->tempat_lahir)) {
                $updateData['tempat_lahir'] = $dp->tempat_lahir;
            }
            if (!empty($dp->tanggal_lahir)) {
                $updateData['tanggal_lahir'] = $dp->tanggal_lahir;
            }
            if (!empty($dp->pekerjaan)) {
                $updateData['penghasilan'] = $penerima->penghasilan; // Tetap simpan penghasilan
            }

            if (!empty($updateData)) {
                $penerima->update($updateData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data penerima ' . $penerima->nama . ' berhasil diperbarui sesuai data resmi Dataguse!',
                'updated_fields' => array_keys($updateData),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung ke Dataguse: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Batch Sync / Terapkan Massal Data Terpilih
     */
    public function syncBatch(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih setidaknya satu penerima untuk di-sync.'
            ], 400);
        }

        $penerimas = DataPenerima::whereIn('id', $ids)->get();
        $niks = $penerimas->pluck('no_ktp')->map(fn($v) => trim($v))->filter()->toArray();

        $updatedCount = 0;

        try {
            $pendudukRows = DB::connection('dataguse')
                ->table('data_penduduks')
                ->whereIn('nomor_induk_kependudukan', $niks)
                ->get()
                ->keyBy('nomor_induk_kependudukan');

            foreach ($penerimas as $penerima) {
                $nik = trim($penerima->no_ktp);
                if (isset($pendudukRows[$nik])) {
                    $dp = $pendudukRows[$nik];
                    $updateData = [];

                    if (!empty($dp->nama))           $updateData['nama'] = $dp->nama;
                    if (!empty($dp->alamat))         $updateData['alamat'] = $dp->alamat;
                    $dgDusun = $dp->dusun ?? $dp->dukuh ?? $dp->kampung ?? null;
                    if (!empty($dgDusun))            $updateData['dusun'] = $dgDusun;
                    $dgDesa = $dp->desa_kelurahan ?? $dp->kelurahan ?? $dp->desa ?? null;
                    if (!empty($dgDesa))             $updateData['desa_kelurahan'] = $dgDesa;
                    if (!empty($dp->kecamatan))      $updateData['kecamatan'] = $dp->kecamatan;
                    $dgKab = $dp->kabupaten ?? $dp->kabupaten_kota ?? null;
                    if (!empty($dgKab))              $updateData['kabupaten_kota'] = $dgKab;
                    if (!empty($dp->nomor_kartu_keluarga)) $updateData['no_kk'] = $dp->nomor_kartu_keluarga;
                    if (!empty($dp->tempat_lahir))   $updateData['tempat_lahir'] = $dp->tempat_lahir;
                    if (!empty($dp->tanggal_lahir))  $updateData['tanggal_lahir'] = $dp->tanggal_lahir;

                    if (!empty($updateData)) {
                        $penerima->update($updateData);
                        $updatedCount++;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil memperbarui {$updatedCount} data penerima dari Dataguse!",
                'count'   => $updatedCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal sync batch: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper normalisasi string untuk pembandingan fleksibel
     */
    private function cleanStr(?string $str): string
    {
        if (!$str) return '';
        $s = mb_strtolower(trim($str));
        return preg_replace('/\s+/', ' ', $s);
    }

    private function cleanKab(?string $str): string
    {
        $s = $this->cleanStr($str);
        $s = preg_replace('/^(kab\.|kabupaten|kota)\s+/i', '', $s);
        return trim($s);
    }

    private function cleanAlamat(?string $str): string
    {
        $s = $this->cleanStr($str);
        $s = preg_replace('/(\(|\,)?\s*rt\s*\d+.*$/i', '', $s);
        return trim($s);
    }
}
