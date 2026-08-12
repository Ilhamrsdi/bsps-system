<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    /**
     * Update password pengguna yang sedang login
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.min'              => 'Password baru minimal 6 karakter.',
            'password.confirmed'        => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])
                ->withInput()
                ->with('active_tab', 'password');
        }

        $user->password       = Hash::make($request->password);
        $user->plain_password = $request->password;
        $user->save();

        return redirect()->back()
            ->with('success_password', 'Password akun Anda berhasil diperbarui!')
            ->with('active_tab', 'password');
    }
}

