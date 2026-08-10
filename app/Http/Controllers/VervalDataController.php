<?php

namespace App\Http\Controllers;

use App\Models\DataPenerima;
use Illuminate\Http\Request;

class VervalDataController extends Controller
{
    /**
     * Tampilkan Halaman Daftar Data Verval Calon Penerima BSPS
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $kecamatan = $request->get('kecamatan', 'all');
        $desil = $request->get('desil', 'all');

        $query = DataPenerima::query();

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

        $totalPenerima = DataPenerima::distinct('no_ktp')->count('no_ktp');
        $totalKecamatan = DataPenerima::distinct('kecamatan')->count('kecamatan');
        $totalDesa = DataPenerima::selectRaw("COUNT(DISTINCT CONCAT(kecamatan, ' - ', desa_kelurahan)) as total")->value('total');

        $stats = [
            'total'     => $totalPenerima,
            'kecamatan' => $totalKecamatan,
            'desa'      => $totalDesa,
            'filter'    => $query->count(),
        ];

        // Daftar Kecamatan untuk filter dropdown
        $listKecamatan = DataPenerima::distinct()->orderBy('kecamatan', 'asc')->pluck('kecamatan')->filter()->values();

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

        return view('verval_data.surat_pernyataan', compact('items'));
    }

    /**
     * Cetak Surat Pernyataan secara Kolektif (Batch Print per Filter/Desa/Kecamatan)
     */
    public function suratPernyataanKolektif(Request $request)
    {
        $search = $request->get('search');
        $kecamatan = $request->get('kecamatan', 'all');
        $desil = $request->get('desil', 'all');

        $query = DataPenerima::query();

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

        $items = $this->attachKadesInfo($query->get());

        return view('verval_data.surat_pernyataan', compact('items'));
    }

    /**
     * Helper untuk melampirkan Data Nama & Jabatan Kepala Desa/Lurah ke Koleksi Penerima dari Database MySQL
     */
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
            'jenis_kepemilikan_lahan' => 'nullable|string',
            'foto_sudut_depan' => 'nullable|image|max:5120',
            'foto_sudut_belakang' => 'nullable|image|max:5120',
            'foto_bagian_dalam' => 'nullable|image|max:5120',
            'foto_sudut_kiri' => 'nullable|image|max:5120',
            'foto_sudut_kanan' => 'nullable|image|max:5120',
        ]);

        $data = $request->except(['ktp', 'kk', 'sertifikat_tanah', 'foto_sudut_depan', 'foto_sudut_belakang', 'foto_bagian_dalam', 'foto_sudut_kiri', 'foto_sudut_kanan', '_token', '_method']);

        // Handle file uploads with auto compression
        $fileFields = ['ktp', 'kk', 'sertifikat_tanah', 'foto_sudut_depan', 'foto_sudut_belakang', 'foto_bagian_dalam', 'foto_sudut_kiri', 'foto_sudut_kanan'];
        
        $uploadPath = storage_path('app/public/uploads');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
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
                    
                    $data[$field] = 'uploads/' . $filename;
                } catch (\Exception $e) {
                    // Fallback to normal store if intervention fails
                    $path = $file->store('uploads', 'public');
                    $data[$field] = $path;
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
