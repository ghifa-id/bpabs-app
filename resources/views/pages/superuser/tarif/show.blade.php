@extends('layouts.app')

@section('title', 'Detail Tarif')

@section('header', 'Detail Tarif')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Tarif</h2>
        <div class="flex space-x-2">
            <a href="{{ route('superuser.tarif.edit', $tarif->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <a href="{{ route('superuser.tarif.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
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
        <!-- Card Informasi Tarif -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                Informasi Tarif
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">ID Tarif</label>
                    <p class="text-gray-800 font-medium">{{ $tarif->id }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nama Tarif</label>
                    <p class="text-gray-800 font-medium text-lg">{{ $tarif->nama_tarif }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Harga</label>
                    <div class="bg-green-100 text-green-800 px-3 py-2 rounded-lg inline-block">
                        <span class="text-xl font-bold">Rp {{ number_format($tarif->harga, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Deskripsi -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-file-alt mr-2 text-green-500"></i>
                Deskripsi
            </h3>
            
            <div class="bg-white p-4 rounded-lg border">
                @if($tarif->deskripsi)
                    <p class="text-gray-700 leading-relaxed">{{ $tarif->deskripsi }}</p>
                @else
                    <p class="text-gray-400 italic">Tidak ada deskripsi</p>
                @endif
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
                <p class="text-gray-800">{{ $tarif->created_at ? $tarif->created_at->format('d M Y, H:i') : '-' }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Terakhir diperbarui</label>
                <p class="text-gray-800">{{ $tarif->updated_at ? $tarif->updated_at->format('d M Y, H:i') : '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex justify-end space-x-2">
        <a href="{{ route('superuser.tarif.delete', $tarif->id) }}" 
           class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
            <i class="fas fa-trash mr-2"></i> Hapus Tarif
        </a>
    </div>
</div>
@endsection