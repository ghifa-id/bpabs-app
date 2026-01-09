@extends('layouts.app')

@section('title', 'Tambah Meteran')

@section('header', 'Tambah Meteran Baru')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-6">
        <a href="{{ route('admin.meteran.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
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

    {{-- PERBAIKAN: Debug yang lebih informatif --}}
    @if(isset($pelanggans) && $pelanggans->count() > 0)
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <strong>Info:</strong> Ditemukan {{ $pelanggans->count() }} pelanggan aktif.
        </div>
    @else
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
            <strong>Warning:</strong> Tidak ada pelanggan aktif yang ditemukan.
            <br>
            <small>
                Total semua pelanggan: {{ \App\Models\User::where('role', 'pelanggan')->count() }} |
                Pelanggan dengan status 'active': {{ \App\Models\User::where('role', 'pelanggan')->where('status', 'active')->count() }} |
                Pelanggan tidak soft deleted: {{ \App\Models\User::where('role', 'pelanggan')->whereNull('deleted_at')->count() }} |
                Pelanggan aktif dan tidak deleted: {{ \App\Models\User::where('role', 'pelanggan')->where('status', 'active')->whereNull('deleted_at')->count() }}
            </small>
        </div>
    @endif

    <form action="{{ route('admin.meteran.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Nomor Meteran Field - READ ONLY -->
        <div>
            <label for="nomor_meteran" class="block text-sm font-medium text-gray-700 mb-2">
                Nomor Meteran <span class="text-green-500">(Otomatis)</span>
            </label>
            <div class="relative">
                <input type="text" 
                       name="nomor_meteran" 
                       id="nomor_meteran" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700" 
                       value="{{ $nomorMeteran ?? 'Loading...' }}" 
                       readonly>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
            </div>
            <p class="mt-1 text-xs text-green-600">
                <i class="fas fa-info-circle mr-1"></i>
                Nomor meteran dibuat otomatis oleh sistem dengan format 00-1XXXXXX
            </p>
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
                @if(isset($pelanggans) && $pelanggans->count() > 0)
                    @foreach($pelanggans as $pelanggan)
                    <option value="{{ $pelanggan->id }}" {{ old('user_id') == $pelanggan->id ? 'selected' : '' }}>
                        {{ $pelanggan->name }} - {{ $pelanggan->email }}
                        @if($pelanggan->username)
                            ({{ $pelanggan->username }})
                        @endif
                    </option>
                    @endforeach
                @else
                    {{-- PERBAIKAN: Fallback dengan query yang benar --}}
                    @php
                        $fallbackPelanggans = \App\Models\User::where('role', 'pelanggan')
                                                             ->where('status', 'active')
                                                             ->whereNull('deleted_at')
                                                             ->orderBy('name')
                                                             ->get();
                    @endphp
                    @if($fallbackPelanggans->count() > 0)
                        @foreach($fallbackPelanggans as $pelanggan)
                        <option value="{{ $pelanggan->id }}" {{ old('user_id') == $pelanggan->id ? 'selected' : '' }}>
                            {{ $pelanggan->name }} - {{ $pelanggan->email }}
                            @if($pelanggan->username)
                                ({{ $pelanggan->username }})
                            @endif
                        </option>
                        @endforeach
                    @else
                        <option value="" disabled>Tidak ada pelanggan aktif</option>
                    @endif
                @endif
            </select>
            <p class="mt-1 text-xs text-gray-500">Pilih pelanggan yang akan menggunakan meteran ini</p>
            
            {{-- Informasi tambahan jika tidak ada pelanggan --}}
            @if((!isset($pelanggans) || $pelanggans->count() == 0) && \App\Models\User::where('role', 'pelanggan')->where('status', 'active')->whereNull('deleted_at')->count() == 0)
            <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-1"></i>
                    Belum ada pelanggan aktif. 
                    <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 underline">
                        Kelola pelanggan terlebih dahulu
                    </a>
                </p>
            </div>
            @endif
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
                           {{ old('status', 'aktif') == 'aktif' ? 'checked' : '' }}>
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
                           {{ old('status') == 'nonaktif' ? 'checked' : '' }}>
                    <label for="status_nonaktif" class="ml-2 block text-sm text-gray-700">
                        Nonaktif
                    </label>
                </div>
            </div>
        </div>

        <!-- Preview Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-blue-800 mb-2">
                <i class="fas fa-eye mr-1"></i> Preview Meteran
            </h4>
            <div class="text-sm text-blue-700">
                <p><strong>Nomor Meteran:</strong> <span id="preview-nomor">{{ $nomorMeteran ?? 'Loading...' }}</span></p>
                <p><strong>Pelanggan:</strong> <span id="preview-pelanggan">Belum dipilih</span></p>
                <p><strong>Status:</strong> <span id="preview-status">Aktif</span></p>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                <i class="fas fa-save mr-2"></i> Simpan Meteran
            </button>
        </div>
    </form>
</div>

<script>
// Update preview saat form berubah
document.addEventListener('DOMContentLoaded', function() {
    const userSelect = document.getElementById('user_id');
    const statusRadios = document.querySelectorAll('input[name="status"]');
    const previewPelanggan = document.getElementById('preview-pelanggan');
    const previewStatus = document.getElementById('preview-status');

    // Update preview pelanggan
    userSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            previewPelanggan.textContent = selectedOption.text;
        } else {
            previewPelanggan.textContent = 'Belum dipilih';
        }
    });

    // Update preview status
    statusRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.checked) {
                previewStatus.textContent = this.value.charAt(0).toUpperCase() + this.value.slice(1);
            }
        });
    });
});
</script>
@endsection