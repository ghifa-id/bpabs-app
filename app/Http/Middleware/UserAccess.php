<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserAccess
{
    public function handle(Request $request, Closure $next, $userType): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Cek apakah user memiliki role yang sesuai
        if ($user->role === $userType) {
            return $next($request);
        }

        // Redirect berdasarkan role user yang sebenarnya
        return match($user->role) {
            'superuser' => redirect()->route('superuser.dashboard.index'),
            'admin' => redirect()->route('admin.dashboard.index'),
            'pelanggan' => redirect()->route('pelanggan.dashboard.index'),
            'petugas' => redirect()->route('petugas.dashboard.index'),
            default => redirect()->route('login')
        };
    }
}