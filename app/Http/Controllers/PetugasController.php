<?php

namespace App\Http\Controllers;

use App\Models\DataPenerima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasController extends Controller
{
    /**
     * Helper Query Calon Penerima di Desa Petugas yang Login
     */
    private function getPetugasQuery()
    {
        $user = Auth::user();
        $petugasId        = $user->id;
        $petugasDesa      = $user->desa;
        $petugasKecamatan = $user->kecamatan;

        return DataPenerima::where(function ($q) use ($petugasId, $petugasDesa, $petugasKecamatan) {
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
    }

    /**
     * Dashboard Khusus Petugas / Fasilitator Lapangan
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $query = $this->getPetugasQuery();

        $totalTugas     = (clone $query)->count();
        $sudahSurvei    = (clone $query)->sudahSurvei()->count();
        $belumSurvei    = (clone $query)->belumSurvei()->count();
        $usulanBaruCount = (clone $query)->where(function ($q) {
            $q->where('pengelompokan_desil', 'like', '%Usulan%')
              ->orWhere('status', 'Usulan Petugas');
        })->count();
        $lakiCount      = (clone $query)->where('jenis_kelamin', 'L')->count();
        $perempuanCount = (clone $query)->where('jenis_kelamin', 'P')->count();
        $backlog1Count  = (clone $query)->where('pengelompokan_desil', 'like', '%Backlog 1%')->count();
        $backlog2Count  = (clone $query)->where('pengelompokan_desil', 'like', '%Backlog 2%')->count();
        $persentase     = $totalTugas > 0 ? round(($sudahSurvei / $totalTugas) * 100) : 0;

        $stats = [
            'total_tugas'        => $totalTugas,
            'sudah_survei'       => $sudahSurvei,
            'belum_survei'       => $belumSurvei,
            'usulan_baru'        => $usulanBaruCount,
            'laki_count'         => $lakiCount,
            'perempuan_count'    => $perempuanCount,
            'backlog1_count'     => $backlog1Count,
            'backlog2_count'     => $backlog2Count,
            'persentase_selesai' => $persentase,
            'desa'               => $user->desa,
            'kecamatan'          => $user->kecamatan,
        ];

        // Search & Filter
        $search = $request->get('search');
        $statusFilter = $request->get('status', 'all');
        $tableQuery = $this->getPetugasQuery();

        if ($search) {
            $tableQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_ktp', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($statusFilter === 'sudah') {
            $tableQuery->sudahSurvei();
        } elseif ($statusFilter === 'belum') {
            $tableQuery->belumSurvei();
        }

        $vervals = $tableQuery->orderBy('id', 'asc')->paginate(15)->withQueryString();
        $allPenerimas = (clone $this->getPetugasQuery())
            ->select('id', 'nama', 'no_ktp', 'no_kk', 'alamat', 'jenis_kelamin', 'pengelompokan_desil', 'status', 'foto_sudut_depan')
            ->orderBy('id', 'asc')
            ->get();

        return view('petugas.dashboard', compact('user', 'stats', 'vervals', 'allPenerimas', 'search', 'statusFilter'));
    }

    /**
     * Halaman Tugas Belum Di-survei
     */
    public function belumSurvei(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');

        $query = $this->getPetugasQuery()->belumSurvei();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_ktp', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $penerimas = $query->orderBy('id', 'asc')->paginate(20)->withQueryString();
        $allPenerimas = (clone $this->getPetugasQuery())
            ->belumSurvei()
            ->select('id', 'nama', 'no_ktp', 'no_kk', 'alamat', 'jenis_kelamin', 'pengelompokan_desil', 'status')
            ->orderBy('id', 'asc')
            ->get();

        return view('petugas.belum_survei', compact('user', 'penerimas', 'allPenerimas', 'search'));
    }

    /**
     * Halaman Tugas Sudah Di-survei
     */
    public function sudahSurvei(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');

        $query = $this->getPetugasQuery()->sudahSurvei();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_ktp', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $penerimas = $query->orderBy('updated_at', 'desc')->paginate(20)->withQueryString();
        $allPenerimas = (clone $this->getPetugasQuery())
            ->sudahSurvei()
            ->select('id', 'nama', 'no_ktp', 'no_kk', 'alamat', 'jenis_kelamin', 'pengelompokan_desil', 'status', 'foto_sudut_depan', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('petugas.sudah_survei', compact('user', 'penerimas', 'allPenerimas', 'search'));
    }

    /**
     * Update Live GPS Location Petugas
     */
    public function updateLocation(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Koordinat GPS Petugas berhasil diperbarui.'
        ]);
    }

    /**
     * Update Status Keberadaan Calon Penerima via Ajax (dari Modal Dashboard)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:ditemukan,meninggal,pindah,tidak diketahui',
        ]);

        $penerima = DataPenerima::findOrFail($id);
        $penerima->status = $request->status;
        $penerima->save();

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui menjadi "' . $request->status . '".',
            'status'  => $request->status,
        ]);
    }

    /**
     * Petugas Menambahkan Usulan Calon Penerima Baru di Desanya
     */
    public function storeUsulan(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama'   => 'required|string|max:255',
            'no_ktp' => 'required|numeric|digits:16',
            'no_kk'  => 'nullable|numeric|digits:16',
        ], [
            'nama.required'   => 'Nama calon penerima wajib diisi.',
            'no_ktp.required' => 'NIK (No. KTP) wajib diisi.',
            'no_ktp.numeric'  => 'NIK harus berupa angka 16 digit.',
            'no_ktp.digits'   => 'NIK harus tepat 16 digit.',
            'no_kk.numeric'   => 'Nomor KK harus berupa angka 16 digit.',
            'no_kk.digits'    => 'Nomor KK harus tepat 16 digit.',
        ]);

        $noKtp = trim($request->no_ktp);
        
        // Cek duplikasi NIK di database
        $existing = DataPenerima::where('no_ktp', $noKtp)->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', "NIK {$noKtp} sudah terdaftar dalam sistem atas nama {$existing->nama} (Desa {$existing->desa_kelurahan}).");
        }

        $alamat = trim($request->input('alamat', ''));
        $rt = trim($request->input('rt', ''));
        $rw = trim($request->input('rw', ''));

        if (($rt || $rw) && !preg_match('/rt\s*\d+/i', $alamat)) {
            $rtLabel = $rt ? "RT" . str_pad($rt, 3, '0', STR_PAD_LEFT) : "";
            $rwLabel = $rw ? "RW" . str_pad($rw, 3, '0', STR_PAD_LEFT) : "";
            $rtrwTag = trim("{$rtLabel} {$rwLabel}");
            if ($rtrwTag) {
                $alamat = $alamat ? "{$alamat} {$rtrwTag}" : $rtrwTag;
            }
        }

        $penerima = DataPenerima::create([
            'user_id'             => $user->id,
            'nama'                => trim($request->nama),
            'no_ktp'              => $noKtp,
            'no_kk'               => trim($request->no_kk),
            'alamat'              => $alamat,
            'dusun'               => trim($request->dusun),
            'rt'                  => $rt,
            'rw'                  => $rw,
            'desa_kelurahan'      => $user->desa,
            'kecamatan'           => $user->kecamatan,
            'kabupaten_kota'      => 'Jember',
            'jenis_kelamin'       => $request->input('jenis_kelamin', 'L'),
            'pengelompokan_desil' => $request->input('pengelompokan_desil', 'Usulan Baru Lapangan'),
            'status'              => 'Usulan Petugas',
        ]);

        if ($request->has('survei_sekarang')) {
            return redirect()->route('survey', ['id' => $penerima->id])->with('success', "Calon penerima '{$penerima->nama}' berhasil ditambahkan! Silakan lengkapi data survei & foto.");
        }

        return redirect()->route('petugas.belum-survei')->with('success', "Calon penerima '{$penerima->nama}' (NIK: {$noKtp}) berhasil diusulkan ke Desa {$user->desa}! Data telah tersimpan di daftar Belum Survei.");
    }
}
