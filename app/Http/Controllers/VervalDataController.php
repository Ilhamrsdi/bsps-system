<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DummyVervalService;

class VervalDataController extends Controller
{
    public function index(Request $request)
    {
        $allData = DummyVervalService::getVervalData();
        $stats = DummyVervalService::getStats();

        $search = $request->query('search');
        $status = $request->query('status');
        $kecamatan = $request->query('kecamatan');

        $filteredData = collect($allData);

        if (!empty($search)) {
            $filteredData = $filteredData->filter(function ($item) use ($search) {
                return stripos($item['nama_kk'], $search) !== false
                    || stripos($item['nik'], $search) !== false
                    || stripos($item['desa'], $search) !== false
                    || stripos($item['fasilitator'], $search) !== false;
            });
        }

        if (!empty($status)) {
            $filteredData = $filteredData->filter(function ($item) use ($status) {
                return $item['status_verval'] === $status;
            });
        }

        if (!empty($kecamatan)) {
            $filteredData = $filteredData->filter(function ($item) use ($kecamatan) {
                return $item['kecamatan'] === $kecamatan;
            });
        }

        return view('verval_data.index', [
            'vervalData' => $filteredData->values(),
            'stats'      => $stats,
            'search'     => $search,
            'status'     => $status,
            'kecamatan'  => $kecamatan,
        ]);
    }
}
