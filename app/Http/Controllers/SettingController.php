<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\DataMingguan;
use App\Models\User;

class SettingController extends Controller
{
    public function index()
    {
        // 1. Log Aktivitas Petugas (Pengisian Survei, Upload Foto, Verifikasi GPS)
        $logPetugas = Survey::with(['dataMingguan', 'user'])
            ->latest('updated_at')
            ->take(30)
            ->get();

        // 2. Log Sesi Login & Perangkat Pengguna / Admin (IP, Device, Lokasi GPS)
        $adminLogUsers = User::orderByRaw('last_location_at IS NULL, last_location_at DESC')
            ->latest('updated_at')
            ->get();

        return view('setting.index', compact('logPetugas', 'adminLogUsers'));
    }
}
