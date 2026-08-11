<?php

namespace App\Http\Controllers;

use App\Models\DataPenerima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VervalDataController extends Controller
{
    /**
     * Tampilkan Halaman Daftar Data Verval Calon Penerima BSPS
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $kecamatan = $request->get('kecamatan', 'all');
        $desil = $request->get('desil', 'all');

        if ($user && $user->isAdminKecamatan()) {
            $kecamatan = $user->kecamatan;
        }

        $query = DataPenerima::query();

        if ($user && $user->isAdminKecamatan()) {
            $query->where('kecamatan', $user->kecamatan);
        } elseif ($kecamatan && $kecamatan !== 'all') {
            $query->where('kecamatan', $kecamatan);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_ktp', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('desa_kelurahan', 'like', "%{$search}%");
            });
        }

        if ($desil && $desil !== 'all') {
            $query->where('pengelompokan_desil', 'like', "%{$desil}%");
        }

        $baseQuery = DataPenerima::query();
        if ($user && $user->isAdminKecamatan()) {
            $baseQuery->where('kecamatan', $user->kecamatan);
        }

        $totalPenerima = (clone $baseQuery)->distinct('no_ktp')->count('no_ktp');
        $totalKecamatan = (clone $baseQuery)->distinct('kecamatan')->count('kecamatan');
        $totalDesa = (clone $baseQuery)->selectRaw("COUNT(DISTINCT CONCAT(kecamatan, ' - ', desa_kelurahan)) as total")->value('total');

        $stats = [
            'total'     => $totalPenerima,
            'kecamatan' => $totalKecamatan,
            'desa'      => $totalDesa,
            'filter'    => $query->count(),
        ];

        // Daftar Kecamatan untuk filter dropdown
        if ($user && $user->isAdminKecamatan()) {
            $listKecamatan = collect([$user->kecamatan]);
        } else {
            $listKecamatan = DataPenerima::distinct()->orderBy('kecamatan', 'asc')->pluck('kecamatan')->filter()->values();
        }

        $vervals = $query->paginate(20)->withQueryString();

        return view('verval_data.index', compact('vervals', 'stats', 'listKecamatan'));
    }

    /**
     * Cetak Surat Pernyataan untuk 1 Penerima
     */
    public function suratPernyataan($id)
    {
        $item = DataPenerima::findOrFail($id);
        $items = $this->attachKadesInfo(collect([$item]));
        $items = $this->enrichFromDataguse($items);

        return view('verval_data.surat_pernyataan', compact('items'));
    }

    /**
     * Cetak Lampiran Foto untuk 1 Penerima
     */
    public function lampiranFoto($id)
    {
        $item = DataPenerima::findOrFail($id);
        $items = $this->attachKadesInfo(collect([$item]));
        $items = $this->enrichFromDataguse($items);

        return view('verval_data.lampiran_foto', compact('items'));
    }

    /**
     * Cetak Surat Pernyataan secara Kolektif (Batch Print per Filter/Desa/Kecamatan)
     */
    public function suratPernyataanKolektif(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $kecamatan = $request->get('kecamatan', 'all');
        $desa = $request->get('desa', 'all');
        $desil = $request->get('desil', 'all');
        $statusFilter = $request->get('status', 'all');

        $query = DataPenerima::query();

        if ($user && $user->isAdminKecamatan()) {
            $query->where('kecamatan', $user->kecamatan);
        } elseif ($user && $user->isPetugas()) {
            $petugasId        = $user->id;
            $petugasDesa      = $user->desa;
            $petugasKecamatan = $user->kecamatan;
            $query->where(function ($q) use ($petugasId, $petugasDesa, $petugasKecamatan) {
                $q->where('user_id', $petugasId);
                if ($petugasDesa) {
                    $q->orWhere(function ($sub) use ($petugasDesa, $petugasKecamatan) {
                        $sub->where('desa_kelurahan', $petugasDesa);
                        if ($petugasKecamatan) {
                            $sub->where('kecamatan', $petugasKecamatan);
                        }
                    });
                }
            });
        } elseif ($desa && $desa !== 'all') {
            $query->where('desa_kelurahan', $desa);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_ktp', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('desa_kelurahan', 'like', "%{$search}%");
            });
        }

        if ($kecamatan && $kecamatan !== 'all') {
            $query->where('kecamatan', $kecamatan);
        }

        if ($desil && $desil !== 'all') {
            $query->where('pengelompokan_desil', 'like', "%{$desil}%");
        }

        if ($statusFilter === 'sudah') {
            $query->sudahSurvei();
        } elseif ($statusFilter === 'belum') {
            $query->belumSurvei();
        }

        $paginator = $query->paginate(50)->withQueryString();
        $items = collect($paginator->items());
        $items = $this->attachKadesInfo($items);
        $items = $this->enrichFromDataguse($items);

        return view('verval_data.surat_pernyataan', compact('items', 'paginator'));
    }

    /**
     * Cetak Lampiran Foto secara Kolektif (Batch Print per Filter/Desa/Kecamatan)
     */
    public function lampiranFotoKolektif(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $kecamatan = $request->get('kecamatan', 'all');
        $desa = $request->get('desa', 'all');
        $desil = $request->get('desil', 'all');
        $statusFilter = $request->get('status', 'all');

        $query = DataPenerima::query();

        if ($user && $user->isAdminKecamatan()) {
            $query->where('kecamatan', $user->kecamatan);
        } elseif ($user && $user->isPetugas()) {
            $petugasId        = $user->id;
            $petugasDesa      = $user->desa;
            $petugasKecamatan = $user->kecamatan;
            $query->where(function ($q) use ($petugasId, $petugasDesa, $petugasKecamatan) {
                $q->where('user_id', $petugasId);
                if ($petugasDesa) {
                    $q->orWhere(function ($sub) use ($petugasDesa, $petugasKecamatan) {
                        $sub->where('desa_kelurahan', $petugasDesa);
                        if ($petugasKecamatan) {
                            $sub->where('kecamatan', $petugasKecamatan);
                        }
                    });
                }
            });
        } elseif ($desa && $desa !== 'all') {
            $query->where('desa_kelurahan', $desa);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_ktp', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('desa_kelurahan', 'like', "%{$search}%");
            });
        }

        if ($kecamatan && $kecamatan !== 'all') {
            $query->where('kecamatan', $kecamatan);
        }

        if ($desil && $desil !== 'all') {
            $query->where('pengelompokan_desil', 'like', "%{$desil}%");
        }

        if ($statusFilter === 'sudah') {
            $query->sudahSurvei();
        } elseif ($statusFilter === 'belum') {
            $query->belumSurvei();
        }

        $paginator = $query->paginate(50)->withQueryString();
        $items = collect($paginator->items());
        $items = $this->attachKadesInfo($items);
        $items = $this->enrichFromDataguse($items);

        return view('verval_data.lampiran_foto', compact('items', 'paginator'));
    }

    /**
     * Helper untuk melampirkan Data Nama & Jabatan Kepala Desa/Lurah ke Koleksi Penerima dari Database MySQL
     */
    /**
     * Enrich setiap item dengan data kependudukan (tempat_lahir, tanggal_lahir, pekerjaan)
     * dari database dataguse — lookup berdasarkan NIK (nomor_induk_kependudukan = no_ktp).
     * Jika tidak ditemukan di dataguse, data lokal tetap digunakan.
     */
    private function enrichFromDataguse($items)
    {
        // Kumpulkan semua NIK dari koleksi penerima
        $niks = $items->pluck('no_ktp')->filter()->unique()->values()->toArray();

        if (empty($niks)) {
            return $items;
        }

        try {
            // Batch-fetch dari data_penduduks di koneksi dataguse
            $pendudukRows = DB::connection('dataguse')
                ->table('data_penduduks')
                ->whereIn('nomor_induk_kependudukan', $niks)
                ->select('nomor_induk_kependudukan', 'tempat_lahir', 'tanggal_lahir', 'pekerjaan', 'nomor_kartu_keluarga')
                ->get()
                ->keyBy('nomor_induk_kependudukan');

            // Tempelkan data ke setiap item penerima
            $items = $items->map(function ($item) use ($pendudukRows) {
                $nik = $item->no_ktp;
                if ($nik && isset($pendudukRows[$nik])) {
                    $dp = $pendudukRows[$nik];
                    // Override hanya jika data dataguse tersedia
                    if ($dp->tempat_lahir)  $item->tempat_lahir  = $dp->tempat_lahir;
                    if ($dp->tanggal_lahir) $item->tanggal_lahir = $dp->tanggal_lahir;
                    if ($dp->pekerjaan)     $item->pekerjaan     = $dp->pekerjaan;
                    if ($dp->nomor_kartu_keluarga) $item->no_kk  = $dp->nomor_kartu_keluarga;
                    $item->dataguse_found = true;
                } else {
                    $item->dataguse_found = false;
                }
                return $item;
            });
        } catch (\Exception $e) {
            // Jika koneksi dataguse gagal, lanjutkan dengan data lokal saja
            \Log::warning('Dataguse connection failed: ' . $e->getMessage());
        }

        return $items;
    }

    private function attachKadesInfo($items)
    {
        $allKades = \App\Models\KepalaDesa::all();
        $kadesMap = [];

        foreach ($allKades as $kd) {
            $kecKey = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $kd->kecamatan ?? ''));
            $desaKey = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $kd->desa_kelurahan ?? ''));
            $kadesMap[$kecKey . '|||' . $desaKey] = [
                'jabatan' => $kd->jabatan,
                'nama'    => $kd->nama,
            ];
        }

        return $items->map(function ($item) use ($kadesMap) {
            $kecKey = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $item->kecamatan ?? ''));
            $desaKey = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $item->desa_kelurahan ?? ''));
            $lookupKey = $kecKey . '|||' . $desaKey;

            if (isset($kadesMap[$lookupKey])) {
                $item->jabatan_kades = $kadesMap[$lookupKey]['jabatan'];
                $item->nama_kades = $kadesMap[$lookupKey]['nama'];
            } else {
                $isKota = in_array(strtoupper($item->kecamatan ?? ''), ['KALIWATES', 'PATRANG', 'SUMBERSARI']);
                $item->jabatan_kades = $isKota ? 'LURAH ' . strtoupper($item->desa_kelurahan) : 'KEPALA DESA ' . strtoupper($item->desa_kelurahan);
                $item->nama_kades = null;
            }
            return $item;
        });
    }

    public function edit($id)
    {
        $vervalData = \App\Models\DataPenerima::findOrFail($id);
        return view('verval_data.edit', compact('vervalData'));
    }

    public function update(Request $request, $id)
    {
        $vervalData = \App\Models\DataPenerima::findOrFail($id);
        
        $request->validate([
            'ktp' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'kk' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'sertifikat_tanah' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'surat_pernyataan' => 'nullable|mimes:jpeg,png,jpg,webp,pdf|max:10240',
            'jenis_kepemilikan_lahan' => 'nullable|string',
            'foto_sudut_depan' => 'nullable|image|max:5120',
            'foto_sudut_belakang' => 'nullable|image|max:5120',
            'foto_bagian_dalam' => 'nullable|image|max:5120',
            'foto_sudut_kiri' => 'nullable|image|max:5120',
            'foto_sudut_kanan' => 'nullable|image|max:5120',
        ]);

        $data = $request->except(['ktp', 'kk', 'sertifikat_tanah', 'surat_pernyataan', 'foto_sudut_depan', 'foto_sudut_belakang', 'foto_bagian_dalam', 'foto_sudut_kiri', 'foto_sudut_kanan', '_token', '_method']);

        // Handle file uploads with auto compression
        $fileFields = ['ktp', 'kk', 'sertifikat_tanah', 'surat_pernyataan', 'foto_sudut_depan', 'foto_sudut_belakang', 'foto_bagian_dalam', 'foto_sudut_kiri', 'foto_sudut_kanan'];
        
        $uploadPath = public_path('uploads');
        if (!file_exists($uploadPath)) {
            @mkdir($uploadPath, 0755, true);
        }

        $storageBackup = storage_path('app/public/uploads');
        if (!file_exists($storageBackup)) {
            @mkdir($storageBackup, 0755, true);
        }

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);

                try {
                    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                    $image = $manager->decodePath($file->getRealPath());

                    // Auto compress and resize
                    $image->scaleDown(1200);

                    $filename = uniqid($field . '_') . '.jpg';
                    $destination = $uploadPath . '/' . $filename;

                    $image->save($destination, quality: 75);
                    @chmod($destination, 0644);
                    @$image->save($storageBackup . '/' . $filename, quality: 75);

                    $data[$field] = 'uploads/' . $filename;
                } catch (\Exception $e) {
                    $filename = uniqid($field . '_') . '.' . $file->getClientOriginalExtension();
                    $file->move($uploadPath, $filename);
                    @chmod($uploadPath . '/' . $filename, 0644);
                    @copy($uploadPath . '/' . $filename, $storageBackup . '/' . $filename);
                    $data[$field] = 'uploads/' . $filename;
                }
            }
        }

        $vervalData->update($data);

        return redirect()->route('data-verval')->with('success', 'Data berhasil disimpan!');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:ditemukan,meninggal,pindah,tidak diketahui'
        ]);
        
        $vervalData = \App\Models\DataPenerima::findOrFail($id);
        $vervalData->update(['status' => $request->status]);
        
        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui'
        ]);
    }
}
