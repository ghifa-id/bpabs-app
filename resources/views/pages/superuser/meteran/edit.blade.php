@extends('layouts.app')

@section('title', 'Edit Meteran')

@section('header', 'Edit Meteran')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-6">
        <a href="{{ route('superuser.meteran.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Meteran
        </a>
    </div>

    @if ($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
        <p class="font-bold">Terjadi kesalahan:</p>
        <ul class="list-disc ml-5">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('superuser.meteran.update', $meteran->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Nomor Meteran Field -->
        <div>
            <label for="nomor_meteran" class="block text-sm font-medium text-gray-700 mb-2">
                Nomor Meteran <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   name="nomor_meteran" 
                   id="nomor_meteran" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                   value="{{ old('nomor_meteran', $meteran->nomor_meteran) }}" 
                   required>
            <p class="mt-1 text-xs text-gray-500">Masukkan nomor meteran yang unik</p>
        </div>

        <!-- User ID Field -->
        <div>
            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                Pelanggan <span class="text-red-500">*</span>
            </label>
            <select name="user_id" 
                    id="user_id" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                    required>
                <option value="">-- Pilih Pelanggan --</option>
                @foreach(\App\Models\User::where('role', 'pelanggan')->orderBy('name')->get() as $user)
                <option value="{{ $user->id }}" {{ old('user_id', $meteran->user_id) == $user->id ? 'selected' : '' }}>
                    {{ $user->name }} - {{ $user->email }}
                </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">Pilih pelanggan yang akan menggunakan meteran ini</p>
        </div>

        <!-- Status Field -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Status <span class="text-red-500">*</span>
            </label>
            <div class="flex space-x-4">
                <div class="flex items-center">
                    <input type="radio" 
                           name="status" 
                           id="status_aktif" 
                           value="aktif" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" 
                           {{ old('status', $meteran->status) == 'aktif' ? 'checked' : '' }}>
                    <label for="status_aktif" class="ml-2 block text-sm text-gray-700">
                        Aktif
                    </label>
                </div>
                <div class="flex items-center">
                    <input type="radio" 
                           name="status" 
                           id="status_nonaktif" 
                           value="nonaktif" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" 
                           {{ old('status', $meteran->status) == 'nonaktif' ? 'checked' : '' }}>
                    <label for="status_nonaktif" class="ml-2 block text-sm text-gray-700">
                        Nonaktif
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                <i class="fas fa-save mr-2"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection