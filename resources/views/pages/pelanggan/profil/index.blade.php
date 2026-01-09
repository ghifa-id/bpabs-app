@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Profil Saya</h1>
        <p class="text-gray-600 mt-2">Kelola informasi profil dan keamanan akun Anda</p>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <!-- Profile Header -->
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-8">
                    <div class="flex items-center">
                        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center text-blue-600 text-2xl font-bold">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div class="ml-4 text-white">
                            <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                            <p class="text-blue-100">{{ ucfirst($user->role) }}</p>
                            <div class="flex items-center mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Information -->
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Nama Lengkap</label>
                                <p class="mt-1 text-gray-900">{{ $user->name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">NIK</label>
                                <p class="mt-1 text-gray-900">{{ $user->nik }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Email</label>
                                <p class="mt-1 text-gray-900">{{ $user->email }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Username</label>
                                <p class="mt-1 text-gray-900">{{ $user->username }}</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">No. Telepon</label>
                                <p class="mt-1 text-gray-900">{{ $user->no_hp ?: '-' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Alamat</label>
                                <p class="mt-1 text-gray-900">{{ $user->alamat ?: '-' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Bergabung Sejak</label>
                                <p class="mt-1 text-gray-900">{{ $user->created_at->format('d F Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Meteran Section -->
            <div class="bg-white rounded-lg shadow-md mt-6 overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">Informasi Meteran</h3>
                </div>
                <div class="px-6 py-4">
                    @if($user->meterans->count() > 0)
                        <div class="space-y-4">
                            @foreach($user->meterans as $meteran)
                                <div class="flex items-center justify-between p-3 border rounded-lg">
                                    <div>
                                        <!-- PERUBAHAN DI SINI: ganti no_meteran menjadi nomor_meteran -->
                                        <p class="font-medium text-lg">
                                            @if($meteran->nomor_meteran)
                                                No. Meter: <span class="font-bold">{{ $meteran->nomor_meteran }}</span>
                                            @else
                                                Meteran #{{ $meteran->id }}
                                            @endif
                                        </p>
                                        <div class="mt-2 text-sm text-gray-600">
                                            <p>Status Meteran: {{ ucfirst($meteran->status) }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="px-3 py-1 text-sm rounded-full 
                                            {{ $meteran->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($meteran->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500">Belum ada meteran terdaftar</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="space-y-6">
            <!-- Account Security -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Keamanan Akun</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Status Akun</span>
                        <span class="text-sm font-medium {{ $user->status === 'active' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $user->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Email Terverifikasi</span>
                        <span class="text-sm font-medium {{ $user->email_verified_at ? 'text-green-600' : 'text-red-600' }}">
                            {{ $user->email_verified_at ? 'Ya' : 'Belum' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistik</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ $user->meterans->count() }}</p>
                        <p class="text-sm text-gray-600">Total Meteran</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold text-green-600">{{ $totalTagihans }}</p>
                        <p class="text-sm text-gray-600">Total Tagihan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ... bagian modal dan script tetap sama ... -->
@endsection