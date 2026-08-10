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
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->role !== $role) {
            // Petugas mencoba akses halaman admin-only → redirect ke dashboard + pesan
            return redirect()->route('dashboard')->with(
                'error',
                'Akses ditolak! Halaman ini hanya dapat diakses oleh Administrator.'
            );
        }

        return $next($request);
    }
}
