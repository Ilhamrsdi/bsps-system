<?php

namespace App\Http\Controllers;

use App\Models\DataMingguan;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurveyController extends Controller
{
    /**
     * Tampilkan Halaman Input Form Survey Lapangan
     */
    public function index(Request $request)
    {
        $kegiatans = DataMingguan::with(['petugas', 'surveys'])->orderBy('nama_kegiatan', 'asc')->get();
        $selectedKegiatan = null;
        $existingSurvey = null;

        if ($request->filled('kegiatan_id')) {
            $selectedKegiatan = DataMingguan::with(['petugas', 'surveys'])->find($request->kegiatan_id);
            if ($selectedKegiatan) {
                $existingSurvey = $selectedKegiatan->surveys->first();
            }
        }

        return view('survey.index', compact('kegiatans', 'selectedKegiatan', 'existingSurvey'));
    }

    /**
     * Proses Simpan / Update Data Survey Lapangan ke Database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'data_mingguan_id'      => 'nullable|exists:data_mingguans,id',
            'nama_kegiatan'         => 'nullable|string|max:255',
            'nama_petugas_1'        => 'required|string|max:255',
            'nama_petugas_2'        => 'nullable|string|max:255',
            'tanggal_survei'        => 'required|date',
            'nama_pemohon'          => 'required|string|max:255',
            'nik_pemohon'           => 'nullable|string|max:20',
            'alamat_pemohon'        => 'required|string',
            'jenis_bangunan'        => 'required|string',
            'fungsi_bangunan'       => 'required|string',
            'jumlah_lantai'         => 'required|numeric|min:1',
            'tinggi_bangunan'       => 'nullable|numeric',
            'luas_bangunan'         => 'nullable|numeric',
            'luas_tanah'            => 'nullable|numeric',
            'status_hak_tanah'      => 'nullable|string',
            'kecamatan'             => 'required|string',
            'desa_kelurahan'        => 'required|string',
            'nama_jalan'            => 'required|string',
            'alamat_lokasi'         => 'required|string',
            'latitude'              => 'required|string',
            'longitude'             => 'required|string',
            'item_admin'            => 'nullable|string',
            'catatan_admin'         => 'nullable|string',
            'item_fungsi'           => 'nullable|string',
            'catatan_fungsi'        => 'nullable|string',
            'item_peruntukan'       => 'nullable|string',
            'catatan_peruntukan'    => 'nullable|string',
            'item_tata'             => 'nullable|string',
            'catatan_tata'          => 'nullable|string',
            'item_kelaikan'         => 'nullable|string',
            'catatan_kelaikan'      => 'nullable|string',
            'garis_sempadan_tritis' => 'nullable|numeric',
            'jarak_as_jalan'        => 'nullable|numeric',
            'pelanggaran_sempadan'  => 'nullable|numeric',
            'catatan_survei'        => 'nullable|string',
        ]);

        // Upload Foto Bukti Lapangan
        $photoFields = [
            'foto_admin_1', 'foto_admin_2', 'foto_admin_3',
            'foto_fungsi_1', 'foto_fungsi_2', 'foto_fungsi_3',
            'foto_peruntukan_1', 'foto_peruntukan_2', 'foto_peruntukan_3',
            'foto_tata_1', 'foto_tata_2', 'foto_tata_3',
            'foto_kelaikan_1', 'foto_kelaikan_2', 'foto_kelaikan_3',
            'foto_bangunan', 'foto_akses',
        ];

        $uploadedPaths = [];
        foreach ($photoFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = time() . '_' . $field . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/surveys'), $filename);
                $uploadedPaths[$field] = 'uploads/surveys/' . $filename;
            }
        }

        // Simpan / Perbarui Data Survey ke Database
        $surveyData = array_merge($validated, $uploadedPaths, [
            'user_id' => Auth::id(),
        ]);

        if ($request->filled('data_mingguan_id')) {
            $existingSurvey = Survey::where('data_mingguan_id', $request->data_mingguan_id)->first();
            if ($existingSurvey) {
                $existingSurvey->update($surveyData);
                $survey = $existingSurvey;
            } else {
                $survey = Survey::create($surveyData);
            }

            $dm = DataMingguan::find($request->data_mingguan_id);
            if ($dm) {
                $dm->update(['status' => 'survei']);
            }
        } else {
            $survey = Survey::create($surveyData);
        }

        if (Auth::check() && Auth::user()->isPetugas()) {
            return redirect()->route('petugas.sudah-survei')->with('success', 'Data survei lapangan berhasil disimpan dan diperbarui! Kegiatan kini tersimpan di menu Sudah Survei.');
        }

        return redirect()->back()->with('success', 'Data survei lapangan berhasil disimpan dan diperbarui ke database sistem Dinas PUPR Jember!');
    }

    /**
     * Upload Foto Async AJAX & Auto Save ke Database
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'field_name'       => 'required|string',
            'photo'            => 'required|image|max:10240',
            'data_mingguan_id' => 'nullable|exists:data_mingguans,id',
        ]);

        $field_name = $request->field_name;
        $file = $request->file('photo');
        $filename = time() . '_' . $field_name . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/surveys'), $filename);
        $relativeUrl = 'uploads/surveys/' . $filename;

        // Auto Save ke database jika data_mingguan_id ada
        if ($request->filled('data_mingguan_id')) {
            $survey = Survey::where('data_mingguan_id', $request->data_mingguan_id)->first();
            if ($survey) {
                $survey->update([$field_name => $relativeUrl]);
            }
        }

        return response()->json([
            'success'    => true,
            'file_url'   => asset($relativeUrl),
            'filename'   => $filename,
            'field_name' => $field_name,
            'message'    => 'Foto berhasil diunggah dan disimpan otomatis!'
        ]);
    }
}
