<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PanitiaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Jika role = operator (panitia), batasi akses
        if (Auth::user()->role === 'operator') {
            // Daftar route yang boleh diakses panitia
            $allowedRoutes = [
                // Kiosk
                'absensi.kiosk',
                'absensi.kiosk.store',
                'absensi.counter',
                
                // Logout
                'logout',
                
                // Login
                'login',
                'login.panitia',
                
                // Dashboard (redirect ke kiosk)
                'dashboard',
            ];

            $currentRoute = $request->route() ? $request->route()->getName() : null;

            // Jika route tidak ada di daftar yang diizinkan
            if ($currentRoute && !in_array($currentRoute, $allowedRoutes)) {
                abort(403, '⛔ Anda tidak memiliki akses ke halaman ini. Hanya panitia kiosk.');
            }

            // Redirect ke kiosk jika mencoba akses dashboard
            if ($currentRoute === 'dashboard') {
                return redirect()->route('absensi.kiosk', ['lembaga' => 'semua']);
            }
        }

        return $next($request);
    }
}