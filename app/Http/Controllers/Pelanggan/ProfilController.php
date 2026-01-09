<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Meteran;

class ProfilController extends Controller
{
    /**
     * Display the user's profile page.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Fixed: Remove problematic tarif relationship
        $user->load(['meterans' => function($query) {
            $query->withCount('tagihans'); // Only keep necessary counts
        }]);
        
        // Calculate total tagihans
        $totalTagihans = $user->meterans->sum('tagihans_count');

        return view('pages.pelanggan.profil.index', compact('user', 'totalTagihans'));
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('pages.pelanggan.profil.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'string', 'max:16', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'alamat' => ['nullable', 'string', 'max:500'],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update([
            'name' => $request->name,
            'nik' => $request->nik,
            'email' => $request->email,
            'username' => $request->username,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
        ]);

        return redirect()->route('pelanggan.profil.index')
                        ->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak benar.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('pelanggan.profil.index')
                        ->with('success', 'Password berhasil diperbarui!');
    }

    /**
     * Show user's meteran information.
     */
    public function meteran()
    {
        $user = Auth::user();
        $meterans = $user->meterans()->get();
        
        return view('pages.pelanggan.profil.meteran', compact('user', 'meterans'));
    }
}