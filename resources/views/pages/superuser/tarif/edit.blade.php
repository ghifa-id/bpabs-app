@extends('layouts.app')

@section('title', 'Edit Tarif')

@section('header', 'Edit Tarif')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Form Edit Tarif</h2>
        <a href="{{ route('superuser.tarif.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('superuser.tarif.update', $tarif->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Nama Tarif -->
        <div>
            <label for="nama_tarif" class="block text-sm font-medium text-gray-700 mb-2">
                Nama Tarif <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   name="nama_tarif" 
                   id="nama_tarif" 
                   value="{{ old('nama_tarif', $tarif->nama_tarif) }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                   placeholder="Masukkan nama tarif (contoh: Tarif Rumah Tangga)"
                   required>
            @error('nama_tarif')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Harga -->
        <div>
            <label for="harga" class="block text-sm font-medium text-gray-700 mb-2">
                Harga per Unit <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                <input type="number" 
                       name="harga" 
                       id="harga" 
                       value="{{ old('harga', $tarif->harga) }}"
                       step="0.01"
                       min="0"
                       placeholder="0"
                       class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       required>
            </div>
            <p class="text-xs text-gray-500 mt-1">Masukkan harga dalam Rupiah (tanpa titik atau koma)</p>
            @error('harga')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Deskripsi -->
        <div>
            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                Deskripsi <span class="text-gray-400">(Opsional)</span>
            </label>
            <textarea name="deskripsi" 
                      id="deskripsi" 
                      rows="4"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="Masukkan deskripsi tarif (contoh: Tarif untuk pelanggan rumah tangga dengan pemakaian normal)">{{ old('deskripsi', $tarif->deskripsi) }}</textarea>
            <p class="text-xs text-gray-500 mt-1">Berikan deskripsi detail tentang tarif ini</p>
            @error('deskripsi')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
            <a href="{{ route('superuser.tarif.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                Batal
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-save mr-2"></i> Update Tarif
            </button>
        </div>
    </form>
</div>

<!-- Help Card -->
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-lightbulb text-blue-400 text-xl"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800">Tips Pengisian Form</h3>
            <div class="mt-2 text-sm text-blue-700">
                <ul class="list-disc list-inside space-y-1">
                    <li>Pastikan nama tarif jelas dan mudah dipahami</li>
                    <li>Harga harus berupa angka positif (tidak boleh negatif)</li>
                    <li>Deskripsi membantu menjelaskan karakteristik tarif</li>
                    <li>Semua field bertanda (*) wajib diisi</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hargaInput = document.getElementById('harga');
    
    // Format angka pada input harga
    hargaInput.addEventListener('input', function() {
        let value = this.value.replace(/[^\d.]/g, '');
        this.value = value;
    });

    // Auto-focus pada field pertama
    document.getElementById('nama_tarif').focus();
});
</script>
@endpush
@endsection