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
        $sudahSurvei    = (clone $query)->whereNotNull('foto_sudut_depan')->count();
        $belumSurvei    = (clone $query)->whereNull('foto_sudut_depan')->count();
        $lakiCount      = (clone $query)->where('jenis_kelamin', 'L')->count();
        $perempuanCount = (clone $query)->where('jenis_kelamin', 'P')->count();
        $backlog1Count  = (clone $query)->where('pengelompokan_desil', 'like', '%Backlog 1%')->count();
        $backlog2Count  = (clone $query)->where('pengelompokan_desil', 'like', '%Backlog 2%')->count();
        $persentase     = $totalTugas > 0 ? round(($sudahSurvei / $totalTugas) * 100) : 0;

        $stats = [
            'total_tugas'        => $totalTugas,
            'sudah_survei'       => $sudahSurvei,
            'belum_survei'       => $belumSurvei,
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
            $tableQuery->whereNotNull('foto_sudut_depan');
        } elseif ($statusFilter === 'belum') {
            $tableQuery->whereNull('foto_sudut_depan');
        }

        $vervals = $tableQuery->orderBy('id', 'asc')->paginate(15)->withQueryString();

        return view('petugas.dashboard', compact('user', 'stats', 'vervals', 'search', 'statusFilter'));
    }

    /**
     * Halaman Tugas Belum Di-survei
     */
    public function belumSurvei(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');

        $query = $this->getPetugasQuery()->whereNull('foto_sudut_depan');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_ktp', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $penerimas = $query->orderBy('id', 'asc')->paginate(20)->withQueryString();

        return view('petugas.belum_survei', compact('user', 'penerimas', 'search'));
    }

    /**
     * Halaman Tugas Sudah Di-survei
     */
    public function sudahSurvei(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');

        $query = $this->getPetugasQuery()->whereNotNull('foto_sudut_depan');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_ktp', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $penerimas = $query->orderBy('updated_at', 'desc')->paginate(20)->withQueryString();

        return view('petugas.sudah_survei', compact('user', 'penerimas', 'search'));
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
}
