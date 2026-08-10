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
        $query = User::query();

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

        // Filter Kecamatan
        if ($request->filled('kecamatan') && $request->kecamatan !== 'all') {
            $query->where('kecamatan', $request->kecamatan);
        }

        // Paginate 10 data per halaman dengan mempertahankan query string filter & search
        $users = $query->orderBy('id', 'asc')->paginate(10)->withQueryString();

        // Counter Statistics Global
        $totalCount = User::count();
        $aktifCount = User::where('status', 'aktif')->count();
        $bertugasCount = User::where('status', 'bertugas')->count();
        $cutiCount = User::where('status', 'cuti')->count();

        return view('user.index', compact('users', 'totalCount', 'aktifCount', 'bertugasCount', 'cutiCount'));
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
            'role' => 'required|in:admin,petugas',
            'password' => 'required|string|min:6',
        ]);

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
            'role' => 'required|in:admin,petugas',
            'password' => 'nullable|string|min:6',
        ]);

        if ($request->filled('password')) {
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

        return redirect()->back()->with('success', 'Petugas survei berhasil dihapus dari database!');
    }
}
