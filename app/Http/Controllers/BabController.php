<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bap;

class BabController extends Controller
{
    public function index()
    {
        return view('bab.index');
    }

    public function cetak($id = null)
    {
        if (!$id) {
            abort(404, 'BAP / ID Kegiatan tidak ditemukan');
        }

        $bap = Bap::with(['dataMingguan.petugas', 'dataMingguan.surveys', 'user'])
            ->where('id', $id)
            ->orWhere('data_mingguan_id', $id)
            ->first();

        if (!$bap) {
            $dm = \App\Models\DataMingguan::find($id);
            if ($dm) {
                $bap = Bap::create([
                    'nomor_bap'        => Bap::generateNomor(),
                    'data_mingguan_id' => $dm->id,
                    'status'           => 'draft',
                    'user_id'          => \Illuminate\Support\Facades\Auth::id(),
                ]);
                $dm->update(['status_bap' => 'sudah']);
                $bap->load(['dataMingguan.petugas', 'dataMingguan.surveys', 'user']);
            } else {
                abort(404, 'Data BAP / Kegiatan tidak ditemukan');
            }
        }

        return view('bab.cetak', compact('bap'));
    }
}
