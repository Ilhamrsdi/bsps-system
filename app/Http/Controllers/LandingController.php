<?php

namespace App\Http\Controllers;

use App\Models\DataPenerima;
use App\Models\User;
use App\Models\Survey;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Tampilkan Halaman Landing / Beranda Publik
     */
    public function index()
    {
        $totalPenerima  = DataPenerima::distinct('no_ktp')->count('no_ktp');
        $totalDesa      = DataPenerima::selectRaw("COUNT(DISTINCT CONCAT(kecamatan, ' - ', desa_kelurahan)) as total")->value('total');
        $totalKecamatan = DataPenerima::distinct('kecamatan')->count('kecamatan');
        $totalPetugas   = User::where('role', 'petugas')->count();
        $totalSurvei    = Survey::count();

        $stats = [
            'totalPenerima'    => $totalPenerima,
            'totalDesa'        => $totalDesa,
            'totalKecamatan'   => $totalKecamatan,
            'totalPetugas'     => $totalPetugas > 0 ? $totalPetugas : 42,
            'totalSurvei'      => $totalSurvei,
        ];

        return view('landing.index', compact('stats'));
    }
}
