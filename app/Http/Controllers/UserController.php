<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Tampilkan Daftar Petugas Survei / User dari Database dengan Pagination 10 per Halaman
     */
    public function index(Request $request)
    {
        $authUser = \Illuminate\Support\Facades\Auth::user();
        $query = User::query();

        if ($authUser && $authUser->isAdminKecamatan()) {
            $query->where('kecamatan', $authUser->kecamatan);
        } elseif ($request->filled('kecamatan') && $request->kecamatan !== 'all') {
            $query->where('kecamatan', $request->kecamatan);
        }

        // Search Keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        // Filter Status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Paginate 10 data per halaman dengan mempertahankan query string filter & search
        $users = $query->orderBy('id', 'asc')->paginate(10)->withQueryString();

        // Counter Statistics (Scoped untuk Admin Kecamatan)
        $baseQuery = User::query();
        if ($authUser && $authUser->isAdminKecamatan()) {
            $baseQuery->where('kecamatan', $authUser->kecamatan);
        }

        $totalCount = (clone $baseQuery)->count();
        $aktifCount = (clone $baseQuery)->where('status', 'aktif')->count();
        $bertugasCount = (clone $baseQuery)->where('status', 'bertugas')->count();
        $cutiCount = (clone $baseQuery)->where('status', 'cuti')->count();

        // Daftar Kecamatan untuk filter
        if ($authUser && $authUser->isAdminKecamatan()) {
            $kecamatanList = collect([$authUser->kecamatan]);
        } else {
            $kecamatanList = \App\Models\DataPenerima::distinct('kecamatan')
                ->whereNotNull('kecamatan')
                ->orderBy('kecamatan', 'asc')
                ->pluck('kecamatan');
        }

        return view('user.index', compact('users', 'totalCount', 'aktifCount', 'bertugasCount', 'cutiCount', 'kecamatanList'));
    }

    /**
     * Tambah Petugas Survei / User Baru ke Database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nip' => 'nullable|string|max:50',
            'jabatan' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,bertugas,cuti',
            'role' => 'required|in:admin,admin_kecamatan,petugas',
            'password' => 'required|string|min:6',
        ]);

        $validated['plain_password'] = $validated['password'];
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->back()->with('success', 'Petugas survei baru berhasil ditambahkan!');
    }

    /**
     * Update Data Petugas Survei / User di Database
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'nip' => 'nullable|string|max:50',
            'jabatan' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,bertugas,cuti',
            'role' => 'required|in:admin,admin_kecamatan,petugas',
            'password' => 'nullable|string|min:6',
        ]);

        if ($request->filled('password')) {
            $validated['plain_password'] = $request->password;
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->back()->with('success', 'Data petugas survei berhasil diperbarui!');
    }

    /**
     * Hapus Petugas Survei / User dari Database
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'Petugas survei berhasil dihapus dari sistem!');
    }

    /**
     * Download Excel / CSV Akun Admin Kecamatan
     */
    public function exportAdminKecamatan()
    {
        $users = User::where('role', 'admin_kecamatan')->orderBy('kecamatan', 'asc')->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Daftar_Akun_Admin_Kecamatan_BSPS.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Microsoft Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['No', 'Nama Akun', 'Email / Username Login', 'Password', 'Kecamatan', 'Jabatan', 'Status Akun']);

            $no = 1;
            foreach ($users as $u) {
                fputcsv($file, [
                    $no++,
                    $u->name,
                    $u->email,
                    $u->plain_password ?: 'password123',
                    $u->kecamatan,
                    $u->jabatan,
                    ucfirst($u->status ?: 'aktif'),
                ]);
            }
            fclose($file);
        };

        return response()->streamDownload($callback, 'Daftar_Akun_Admin_Kecamatan_BSPS.csv', $headers);
    }
}
