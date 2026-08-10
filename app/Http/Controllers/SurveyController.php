<?php

namespace App\Http\Controllers;

use App\Models\DataPenerima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurveyController extends Controller
{
    /**
     * Tampilkan Form Survei Verifikasi & Validasi Lapangan BSPS (Berdasarkan Data Penerima)
     */
    public function index(Request $request, $id = null)
    {
        $recipientId = $id ?? $request->get('id');
        $nik = $request->get('nik');

        if ($recipientId) {
            $vervalData = DataPenerima::find($recipientId);
        } elseif ($nik) {
            $vervalData = DataPenerima::where('no_ktp', $nik)->first();
        } else {
            $vervalData = DataPenerima::first();
        }

        if (!$vervalData) {
            return redirect()->route('verval-data')->with('error', 'Data Calon Penerima tidak ditemukan.');
        }

        // List ringkas untuk quick search / dropdown pilih calon penerima
        $allPenerimas = DataPenerima::select('id', 'nama', 'no_ktp', 'desa_kelurahan', 'kecamatan')
            ->orderBy('nama', 'asc')
            ->limit(100)
            ->get();

        return view('survey.index', compact('vervalData', 'allPenerimas'));
    }

    /**
     * Simpan / Perbarui Hasil Survei Lapangan & Dokumen Verval BSPS
     */
    public function store(Request $request, $id = null)
    {
        $targetId = $id ?? $request->get('id');
        $vervalData = DataPenerima::findOrFail($targetId);

        $request->validate([
            'tempat_lahir'            => 'required|string|max:255',
            'tanggal_lahir'           => 'required|date',
            'penghasilan'             => 'required|string|max:255',
            'luas_tanah'              => 'required|string|max:255',
            'telah_ditempati_selama'  => 'required|string|max:255',
            'status_tanah'            => 'required|string|max:255',
            'jenis_kepemilikan_lahan' => 'required|string|max:255',
            'indikator_lantai'        => 'required|in:ada,tidak_ada',
            'indikator_pondasi'       => 'required|in:ada,tidak_ada',
            'indikator_dinding'       => 'required|in:ada,tidak_ada',
            'indikator_struktur'      => 'required|in:ada,tidak_ada',
            'indikator_atap'          => 'required|in:ada,tidak_ada',
            'indikator_penghasilan'   => 'required|in:ada,tidak_ada',
            'ktp'                     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'kk'                      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'sertifikat_tanah'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'foto_sudut_depan'        => 'nullable|image|max:5120',
            'foto_sudut_belakang'     => 'nullable|image|max:5120',
            'foto_bagian_dalam'       => 'nullable|image|max:5120',
            'foto_sudut_kiri'         => 'nullable|image|max:5120',
            'foto_sudut_kanan'        => 'nullable|image|max:5120',
        ], [
            'required' => 'Kolom :attribute wajib diisi lengkap.',
            'in'       => 'Pilihan :attribute tidak valid.',
        ]);

        // Cek kelengkapan 8 Berkas & Foto Lapangan
        $missingPhotos = [];
        $photoLabels = [
            'ktp'                 => 'Foto / Scan KTP',
            'kk'                  => 'Foto / Scan Kartu Keluarga',
            'sertifikat_tanah'    => 'Foto Sertipikat / Bukti Tanah',
            'foto_sudut_depan'    => 'Foto Fisik: Tampak Depan',
            'foto_sudut_belakang' => 'Foto Fisik: Tampak Belakang',
            'foto_bagian_dalam'   => 'Foto Fisik: Bagian Dalam / Interior',
            'foto_sudut_kiri'     => 'Foto Fisik: Samping Kiri',
            'foto_sudut_kanan'    => 'Foto Fisik: Samping Kanan',
        ];

        foreach ($photoLabels as $field => $label) {
            if (!$vervalData->$field && !$request->hasFile($field)) {
                $missingPhotos[] = $label;
            }
        }

        if (!empty($missingPhotos)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['foto' => 'Semua berkas dan 5 sudut foto fisik rumah wajib diisi lengkap. Yang belum diunggah: ' . implode(', ', $missingPhotos)]);
        }

        $data = $request->except([
            'ktp', 'kk', 'sertifikat_tanah',
            'foto_sudut_depan', 'foto_sudut_belakang', 'foto_bagian_dalam',
            'foto_sudut_kiri', 'foto_sudut_kanan',
            '_token', '_method', 'id'
        ]);

        $data['indikator_lantai']      = $request->input('indikator_lantai');
        $data['indikator_pondasi']     = $request->input('indikator_pondasi');
        $data['indikator_dinding']     = $request->input('indikator_dinding');
        $data['indikator_struktur']    = $request->input('indikator_struktur');
        $data['indikator_atap']        = $request->input('indikator_atap');
        $data['indikator_penghasilan'] = $request->input('indikator_penghasilan');

        // Hitung total indikator RTLH yang terpenuhi:
        // - Komponen fisik (Lantai, Pondasi, Dinding, Struktur, Atap) bernilai 'tidak_ada'
        // - Penghasilan < UMK bernilai 'ada'
        $totalIndikatorRtlh = 0;
        if ($data['indikator_lantai'] === 'tidak_ada') $totalIndikatorRtlh++;
        if ($data['indikator_pondasi'] === 'tidak_ada') $totalIndikatorRtlh++;
        if ($data['indikator_dinding'] === 'tidak_ada') $totalIndikatorRtlh++;
        if ($data['indikator_struktur'] === 'tidak_ada') $totalIndikatorRtlh++;
        if ($data['indikator_atap'] === 'tidak_ada') $totalIndikatorRtlh++;
        if ($data['indikator_penghasilan'] === 'ada') $totalIndikatorRtlh++;

        $data['status_kelayakan'] = $totalIndikatorRtlh >= 2 ? 'Layak Diusulkan' : 'Tidak Layak Diusulkan';




        // Upload berkas dengan auto compression (jika GD/Intervention terpasang)
        $fileFields = [
            'ktp', 'kk', 'sertifikat_tanah',
            'foto_sudut_depan', 'foto_sudut_belakang', 'foto_bagian_dalam',
            'foto_sudut_kiri', 'foto_sudut_kanan'
        ];

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
                    if (class_exists(\Intervention\Image\ImageManager::class)) {
                        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                        $image = $manager->decodePath($file->getRealPath());
                        $image->scaleDown(1200);

                        $filename = uniqid($field . '_') . '.jpg';
                        $destination = $uploadPath . '/' . $filename;
                        $image->save($destination, quality: 75);
                        @chmod($destination, 0644);

                        // Backup to storage
                        @$image->save($storageBackup . '/' . $filename, quality: 75);
                    } else {
                        $filename = uniqid($field . '_') . '.' . $file->getClientOriginalExtension();
                        $file->move($uploadPath, $filename);
                        @chmod($uploadPath . '/' . $filename, 0644);
                        @copy($uploadPath . '/' . $filename, $storageBackup . '/' . $filename);
                    }

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

        $data['status'] = 'ditemukan';
        $vervalData->update($data);

        if (Auth::user() && Auth::user()->role === 'petugas') {
            return redirect()->route('petugas.sudah-survei')
                ->with('success', 'Data Survei Lapangan untuk ' . $vervalData->nama . ' berhasil disimpan dan otomatis masuk ke daftar Sudah Survei!');
        }

        return redirect()->route('survey', ['id' => $vervalData->id])
            ->with('success', 'Data Survei & Dokumen Verval untuk ' . $vervalData->nama . ' berhasil disimpan!');
    }

    /**
     * Upload & Auto-save Individual Photo via Ajax
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'id'    => 'required|exists:data_penerimas,id',
            'field' => 'required|in:ktp,kk,sertifikat_tanah,foto_sudut_depan,foto_sudut_belakang,foto_bagian_dalam,foto_sudut_kiri,foto_sudut_kanan',
            'photo' => 'required|image|max:10240',
        ]);

        $vervalData = DataPenerima::findOrFail($request->id);
        $field = $request->field;
        $file = $request->file('photo');

        $uploadPath = storage_path('app/public/uploads');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Hapus foto lama jika ada
        if ($vervalData->$field && file_exists(storage_path('app/public/' . $vervalData->$field))) {
            @unlink(storage_path('app/public/' . $vervalData->$field));
        }

        try {
            if (class_exists(\Intervention\Image\ImageManager::class)) {
                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                $image = $manager->decodePath($file->getRealPath());
                $image->scaleDown(1200);

                $filename = uniqid($field . '_') . '.jpg';
                $destination = $uploadPath . '/' . $filename;
                $image->save($destination, quality: 75);
            } else {
                $filename = uniqid($field . '_') . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
            }

            $relativePath = 'uploads/' . $filename;
            $vervalData->$field = $relativePath;
            $vervalData->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Foto berhasil disimpan!',
                'url'     => asset('storage/' . $relativePath),
                'field'   => $field,
                'path'    => $relativePath
            ]);
        } catch (\Exception $e) {
            $filename = uniqid($field . '_') . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            $relativePath = 'uploads/' . $filename;

            $vervalData->$field = $relativePath;
            $vervalData->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Foto berhasil disimpan!',
                'url'     => asset('storage/' . $relativePath),
                'field'   => $field,
                'path'    => $relativePath
            ]);
        }
    }

    /**
     * Delete Individual Photo via Ajax
     */
    public function deletePhoto(Request $request)
    {
        $request->validate([
            'id'    => 'required|exists:data_penerimas,id',
            'field' => 'required|in:ktp,kk,sertifikat_tanah,foto_sudut_depan,foto_sudut_belakang,foto_bagian_dalam,foto_sudut_kiri,foto_sudut_kanan',
        ]);

        $vervalData = DataPenerima::findOrFail($request->id);
        $field = $request->field;

        if ($vervalData->$field && file_exists(storage_path('app/public/' . $vervalData->$field))) {
            @unlink(storage_path('app/public/' . $vervalData->$field));
        }

        $vervalData->$field = null;
        $vervalData->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Foto berhasil dihapus!',
            'field'   => $field
        ]);
    }
}
