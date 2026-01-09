@extends('layouts.app')

@section('title', 'Tambah User')

@section('header', 'Tambah User Baru')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-6">
        <a href="{{ route('superuser.users.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar User
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

    <form action="{{ route('superuser.users.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Name Field -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="name" 
                   name="name" 
                   value="{{ old('name') }}"
                   required
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                   placeholder="Masukkan nama lengkap">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Username Field -->
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                Username
            </label>
            <input type="text" 
                   id="username" 
                   name="username" 
                   value="{{ old('username') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('username') border-red-500 @enderror"
                   placeholder="Masukkan username (opsional)">
            @error('username')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Email <span class="text-red-500">*</span>
            </label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   value="{{ old('email') }}"
                   required
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                   placeholder="contoh@example.com">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Alamat Field -->
        <div>
            <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                Alamat
            </label>
            <textarea id="alamat" 
                      name="alamat" 
                      rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('alamat') border-red-500 @enderror"
                      placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
            @error('alamat')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- No HP Field -->
        <div>
            <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-2">
                Nomor HP
            </label>
            <input type="text" 
                   id="no_hp" 
                   name="no_hp" 
                   value="{{ old('no_hp') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('no_hp') border-red-500 @enderror"
                   placeholder="Contoh: 08123456789">
            @error('no_hp')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password Field -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                Password <span class="text-red-500">*</span>
            </label>
            <input type="password" 
                   id="password" 
                   name="password"
                   required
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                   placeholder="Masukkan password (minimal 8 karakter)">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Role Field -->
        <div>
            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                Role <span class="text-red-500">*</span>
            </label>
            <select id="role" 
                    name="role" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('role') border-red-500 @enderror">
                <option value="">Pilih Role</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="pelanggan" {{ old('role') == 'pelanggan' ? 'selected' : '' }}>Pelanggan</option>
                <option value="petugas" {{ old('role') == 'petugas' ? 'selected' : '' }}>Petugas</option>
                <option value="superuser" {{ old('role') == 'superuser' ? 'selected' : '' }}>Superuser</option>
            </select>
            @error('role')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Status Field -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Status Akun
            </label>
            <div class="flex space-x-4">
                <div class="flex items-center">
                    <input type="radio" 
                           name="status" 
                           id="status_active" 
                           value="active" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" 
                           {{ old('status', 'active') == 'active' ? 'checked' : '' }}>
                    <label for="status_active" class="ml-2 block text-sm text-gray-700">
                        Aktif
                    </label>
                </div>
                <div class="flex items-center">
                    <input type="radio" 
                           name="status" 
                           id="status_inactive" 
                           value="inactive" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" 
                           {{ old('status') == 'inactive' ? 'checked' : '' }}>
                    <label for="status_inactive" class="ml-2 block text-sm text-gray-700">
                        Nonaktif
                    </label>
                </div>
            </div>
        </div>

        <!-- Preview Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-blue-800 mb-2">
                <i class="fas fa-eye mr-1"></i> Preview User
            </h4>
            <div class="text-sm text-blue-700">
                <p><strong>Nama:</strong> <span id="preview-nama">{{ old('name') ?? 'Belum diisi' }}</span></p>
                <p><strong>Email:</strong> <span id="preview-email">{{ old('email') ?? 'Belum diisi' }}</span></p>
                <p><strong>Role:</strong> <span id="preview-role">{{ old('role') ? ucfirst(old('role')) : 'Belum dipilih' }}</span></p>
                <p><strong>Status:</strong> <span id="preview-status">{{ old('status', 'active') == 'active' ? 'Aktif' : 'Nonaktif' }}</span></p>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                <i class="fas fa-save mr-2"></i> Simpan User
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus on name field
    document.getElementById('name').focus();

    // Update preview saat form berubah
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const roleSelect = document.getElementById('role');
    const statusRadios = document.querySelectorAll('input[name="status"]');
    
    const previewNama = document.getElementById('preview-nama');
    const previewEmail = document.getElementById('preview-email');
    const previewRole = document.getElementById('preview-role');
    const previewStatus = document.getElementById('preview-status');

    nameInput.addEventListener('input', function() {
        previewNama.textContent = this.value || 'Belum diisi';
    });

    emailInput.addEventListener('input', function() {
        previewEmail.textContent = this.value || 'Belum diisi';
    });

    roleSelect.addEventListener('change', function() {
        previewRole.textContent = this.value ? this.options[this.selectedIndex].text : 'Belum dipilih';
    });

    statusRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.checked) {
                previewStatus.textContent = this.value === 'active' ? 'Aktif' : 'Nonaktif';
            }
        });
    });

    // Format nomor HP
    const noHpInput = document.getElementById('no_hp');
    noHpInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        
        if (value.length > 15) {
            value = value.slice(0, 15);
        }
        
        if (value.startsWith('62')) {
            this.value = value;
        } else if (value.startsWith('0')) {
            this.value = value;
        } else if (value.length > 0) {
            this.value = '0' + value;
        }
    });
});
</script>
@endpush
@endsection