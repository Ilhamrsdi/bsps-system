<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Cek apakah user yang login memiliki role yang diizinkan.
     *
     * Contoh penggunaan di route:
     *   Route::middleware(['auth', 'role:admin'])
     *
     * @param  string  $role  Role yang diizinkan (misal: 'admin')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        $allowedRoles = [];
        foreach ($roles as $r) {
            foreach (explode(',', $r) as $subRole) {
                $allowedRoles[] = trim($subRole);
            }
        }

        if (!in_array($user->role, $allowedRoles)) {
            if ($user->isPetugas()) {
                return redirect()->route('petugas.dashboard')->with('error', 'Akses ditolak!');
            }
            if ($user->isAdminKecamatan()) {
                return redirect()->route('dashboard.kecamatan')->with('error', 'Akses ditolak!');
            }
            return redirect()->route('dashboard')->with(
                'error',
                'Akses ditolak! Anda tidak memiliki izin untuk mengakses halaman tersebut.'
            );
        }

        return $next($request);
    }
}
