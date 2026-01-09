@extends('layouts.app')

@section('title', 'Detail Meteran')

@section('header', 'Detail Meteran')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Meteran</h2>
        <div class="flex space-x-2">
            <a href="{{ route('superuser.meteran.edit', $meteran->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <a href="{{ route('superuser.meteran.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card Informasi Meteran -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-tachometer-alt mr-2 text-blue-500"></i>
                Informasi Meteran
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">ID Meteran</label>
                    <p class="text-gray-800 font-medium">{{ $meteran->id }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nomor Meteran</label>
                    <p class="text-gray-800 font-bold text-lg">{{ $meteran->nomor_meteran }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
                    <div class="inline-block">
                        @if($meteran->status == 'aktif')
                            <span class="px-3 py-2 bg-green-100 text-green-800 rounded-lg text-sm font-medium">
                                <i class="fas fa-check-circle mr-1"></i> Aktif
                            </span>
                        @else
                            <span class="px-3 py-2 bg-red-100 text-red-800 rounded-lg text-sm font-medium">
                                <i class="fas fa-times-circle mr-1"></i> Nonaktif
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Informasi Pelanggan -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-user mr-2 text-green-500"></i>
                Informasi Pelanggan
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nama Pelanggan</label>
                    <p class="text-gray-800 font-medium text-lg">{{ $meteran->user->nama ?? $meteran->user->name }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                    <p class="text-gray-800">{{ $meteran->user->email }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Telepon</label>
                    <p class="text-gray-800">{{ $meteran->user->telepon ?? $meteran->user->phone ?? '-' }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Alamat</label>
                    <p class="text-gray-800">{{ $meteran->user->alamat ?? '-' }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Status Akun</label>
                    <div class="inline-block">
                        @if($meteran->user->status == 'active')
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                <i class="fas fa-user-check mr-1"></i> Aktif
                            </span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                <i class="fas fa-user-times mr-1"></i> Nonaktif
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Statistik Penggunaan -->
    <div class="mt-6 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-bar mr-2 text-purple-500"></i>
            Statistik Penggunaan
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Tagihan</p>
                        <p class="text-2xl font-bold text-blue-600">
                            {{ $meteran->tagihans->count() ?? 0 }}
                        </p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-file-invoice text-blue-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Sudah Dibayar</p>
                        <p class="text-2xl font-bold text-green-600">
                            {{ $meteran->tagihans->where('status', 'sudah_bayar')->count() ?? 0 }}
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Belum Dibayar</p>
                        <p class="text-2xl font-bold text-red-600">
                            {{ $meteran->tagihans->where('status', 'belum_bayar')->count() ?? 0 }}
                        </p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Terlambat</p>
                        <p class="text-2xl font-bold text-orange-600">
                            {{ $meteran->tagihans->where('status', 'terlambat')->count() ?? 0 }}
                        </p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-exclamation-triangle text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Informasi Timestamp -->
    <div class="mt-6 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-clock mr-2 text-purple-500"></i>
            Informasi Waktu
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Dibuat pada</label>
                <p class="text-gray-800">{{ $meteran->created_at ? $meteran->created_at->format('d M Y, H:i') : '-' }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Terakhir diperbarui</label>
                <p class="text-gray-800">{{ $meteran->updated_at ? $meteran->updated_at->format('d M Y, H:i') : '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex justify-end space-x-2">
        <a href="{{ route('superuser.meteran.delete', $meteran->id) }}" 
           class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
            <i class="fas fa-trash mr-2"></i> Hapus Meteran
        </a>
    </div>
</div>
@endsection