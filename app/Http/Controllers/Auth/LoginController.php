<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$loginType => $request->username, 'password' => $request->password])) {
            $user = Auth::user();
            Session::put('user_id', $user->id);

            // Pastikan field role ada dan ada fallback
            $role = $user->role ?? $user->peran->nama ?? 'unknown';

            return match($role) {
                'pelanggan' => redirect()->route('pelanggan.dashboard.index'),
                'superuser' => redirect()->route('superuser.dashboard.index'),  // ← Ubah ini
                'admin' => redirect()->route('admin.dashboard.index'),
                'petugas' => redirect()->route('petugas.dashboard.index'),
                default => redirect()->route('login')
                    ->withErrors(['login' => 'Role tidak valid.'])
            };
        }

        return redirect()->route('login')
            ->withErrors(['login' => 'Kredensial tidak valid.'])
            ->withInput($request->except('password'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        Session::flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}