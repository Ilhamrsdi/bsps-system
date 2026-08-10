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
            'ktp'                     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'kk'                      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'sertifikat_tanah'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'jenis_kepemilikan_lahan' => 'nullable|string',
            'foto_sudut_depan'        => 'nullable|image|max:5120',
            'foto_sudut_belakang'     => 'nullable|image|max:5120',
            'foto_bagian_dalam'       => 'nullable|image|max:5120',
            'foto_sudut_kiri'         => 'nullable|image|max:5120',
            'foto_sudut_kanan'        => 'nullable|image|max:5120',
        ]);

        $data = $request->except([
            'ktp', 'kk', 'sertifikat_tanah',
            'foto_sudut_depan', 'foto_sudut_belakang', 'foto_bagian_dalam',
            'foto_sudut_kiri', 'foto_sudut_kanan',
            '_token', '_method', 'id'
        ]);

        // Upload berkas dengan auto compression (jika GD/Intervention terpasang)
        $fileFields = [
            'ktp', 'kk', 'sertifikat_tanah',
            'foto_sudut_depan', 'foto_sudut_belakang', 'foto_bagian_dalam',
            'foto_sudut_kiri', 'foto_sudut_kanan'
        ];

        $uploadPath = storage_path('app/public/uploads');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
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
                    } else {
                        $filename = uniqid($field . '_') . '.' . $file->getClientOriginalExtension();
                        $file->move($uploadPath, $filename);
                    }

                    $data[$field] = 'uploads/' . $filename;
                } catch (\Exception $e) {
                    $filename = uniqid($field . '_') . '.' . $file->getClientOriginalExtension();
                    $file->move($uploadPath, $filename);
                    $data[$field] = 'uploads/' . $filename;
                }
            }
        }

        $vervalData->update($data);

        return redirect()->route('survey', ['id' => $vervalData->id])
            ->with('success', 'Data Survei & Dokumen Verval untuk ' . $vervalData->nama . ' berhasil disimpan!');
    }
}
