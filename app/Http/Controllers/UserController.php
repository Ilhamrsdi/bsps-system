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
        $filename = 'Akun_Admin_Kecamatan_BSPS.xls';
        $path = public_path($filename);

        if (!file_exists($path)) {
            $users = User::where('role', 'admin_kecamatan')->orderBy('kecamatan', 'asc')->get();
            $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><style>th { background-color: #002855; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #000000; padding: 8px; } td { border: 1px solid #cccccc; padding: 6px 10px; font-family: Arial; font-size: 11pt; } .center { text-align: center; } .bold { font-weight: bold; }</style></head><body><h2>DAFTAR AKUN ADMIN KECAMATAN BSPS KABUPATEN JEMBER</h2><table><thead><tr><th>No</th><th>Nama Akun</th><th>Email / Username Login</th><th>Password</th><th>Kecamatan</th><th>Jabatan</th><th>Status Akun</th></tr></thead><tbody>';
            $no = 1;
            foreach ($users as $u) {
                $html .= '<tr><td class="center">' . $no++ . '</td><td class="bold">' . htmlspecialchars($u->name) . '</td><td>' . htmlspecialchars($u->email) . '</td><td class="center bold">password123</td><td class="center bold">' . htmlspecialchars($u->kecamatan) . '</td><td>' . htmlspecialchars($u->jabatan) . '</td><td class="center">' . htmlspecialchars(ucfirst($u->status)) . '</td></tr>';
            }
            $html .= '</tbody></table></body></html>';
            file_put_contents($path, $html);
        }

        return response()->download($path, 'Daftar_Akun_Admin_Kecamatan_BSPS.xls', [
            'Content-Type' => 'application/vnd.ms-excel',
        ]);
    }
}
