<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function index()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'alamat' => 'required',
            'no_hp' => 'required|unique:users',
            'email' => 'required|email:dns|unique:users',
            'username' => 'required|min:3|max:255|unique:users',
            'password' => 'required|min:5|max:255',
            'password_confirmation' => 'required|same:password'
        ]);

        $validatedData['role'] = 'pelanggan';
        $validatedData['status'] = 'active';

        User::create($validatedData);

        return redirect('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }
}