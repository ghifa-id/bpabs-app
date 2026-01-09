@extends('layouts.app')

@section('title', 'Tambah Pelanggan')

@section('header', 'Tambah Pelanggan Baru')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Pelanggan
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

    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
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

        <!-- NIK Field -->
        <div>
            <label for="nik" class="block text-sm font-medium text-gray-700 mb-2">
                NIK <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="nik" 
                   name="nik" 
                   value="{{ old('nik') }}"
                   required
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('nik') border-red-500 @enderror"
                   placeholder="Masukkan NIK">
            @error('nik')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Username Field -->
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                Username <span class="text-gray-500">(Opsional)</span>
            </label>
            <input type="text" 
                   id="username" 
                   name="username" 
                   value="{{ old('username') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('username') border-red-500 @enderror"
                   placeholder="Masukkan username (opsional)">
            <p class="mt-1 text-xs text-gray-500">Username harus unik jika diisi</p>
            @error('username')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Email <span class="text-gray-500">(Opsional)</span>
            </label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   value="{{ old('email') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                   placeholder="contoh@example.com">
            <p class="mt-1 text-xs text-gray-500">Email harus unik (tidak boleh sama dengan pengguna lain)</p>
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Alamat Field -->
        <div>
            <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                Alamat <span class="text-gray-500">(Opsional)</span>
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
                Nomor HP <span class="text-gray-500">(Opsional)</span>
            </label>
            <input type="text" 
                   id="no_hp" 
                   name="no_hp" 
                   value="{{ old('no_hp') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('no_hp') border-red-500 @enderror"
                   placeholder="Contoh: 08123456789">
            <p class="mt-1 text-xs text-gray-500">Format: 08xxxxxxxxx atau 62xxxxxxxxx</p>
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
            <p class="mt-1 text-xs text-gray-500">Password minimal 8 karakter</p>
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Role Field (Hidden for pelanggan) -->
        <input type="hidden" name="role" value="pelanggan">

        <!-- Status Field -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Status Akun <span class="text-red-500">*</span>
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
                <i class="fas fa-eye mr-1"></i> Preview Pelanggan
            </h4>
            <div class="text-sm text-blue-700">
                <p><strong>Nama:</strong> <span id="preview-name">Belum diisi</span></p>
                <p><strong>NIK:</strong> <span id="preview-nik">Belum diisi</span></p>
                <p><strong>Username:</strong> <span id="preview-username">Belum diisi</span></p>
                <p><strong>Email:</strong> <span id="preview-email">Belum diisi</span></p>
                <p><strong>No HP:</strong> <span id="preview-no-hp">Belum diisi</span></p>
                <p><strong>Status:</strong> <span id="preview-status">Aktif</span></p>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                <i class="fas fa-save mr-2"></i> Simpan Pelanggan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus on name field
    document.getElementById('name').focus();
    
    // Form elements
    const form = document.querySelector('form');
    const nameInput = document.getElementById('name');
    const nikInput = document.getElementById('nik');
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const noHpInput = document.getElementById('no_hp');
    const statusRadios = document.querySelectorAll('input[name="status"]');
    
    // Preview elements
    const previewName = document.getElementById('preview-name');
    const previewNik = document.getElementById('preview-nik');
    const previewUsername = document.getElementById('preview-username');
    const previewEmail = document.getElementById('preview-email');
    const previewNoHp = document.getElementById('preview-no-hp');
    const previewStatus = document.getElementById('preview-status');
    
    // Update preview - Name
    nameInput.addEventListener('input', function() {
        previewName.textContent = this.value.trim() || 'Belum diisi';
    });
    
    // Update preview - NIK
    nikInput.addEventListener('input', function() {
        previewNik.textContent = this.value.trim() || 'Belum diisi';
    });
    
    // Update preview - Username
    usernameInput.addEventListener('input', function() {
        previewUsername.textContent = this.value.trim() || 'Belum diisi';
    });
    
    // Update preview - Email
    emailInput.addEventListener('input', function() {
        previewEmail.textContent = this.value.trim() || 'Belum diisi';
    });
    
    // Update preview - No HP
    noHpInput.addEventListener('input', function() {
        previewNoHp.textContent = this.value.trim() || 'Belum diisi';
    });
    
    // Update preview - Status
    statusRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.checked) {
                previewStatus.textContent = this.value.charAt(0).toUpperCase() + this.value.slice(1);
            }
        });
    });
    
    // Form validation
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Reset previous error states
        [nameInput, nikInput, emailInput, passwordInput].forEach(input => {
            input.classList.remove('border-red-500');
        });
        
        // Validate name
        if (nameInput.value.trim().length < 2) {
            nameInput.classList.add('border-red-500');
            isValid = false;
        }
        
        // Validate NIK
        if (nikInput.value.trim().length < 16) {
            nikInput.classList.add('border-red-500');
            isValid = false;
        }
        
        // Validate password
        if (passwordInput.value.length < 8) {
            passwordInput.classList.add('border-red-500');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Mohon periksa kembali data yang dimasukkan');
        }
    });
    
    // Real-time validation feedback
    nameInput.addEventListener('input', function() {
        if (this.value.trim().length >= 2) {
            this.classList.remove('border-red-500');
            this.classList.add('border-green-500');
        } else {
            this.classList.remove('border-green-500');
        }
    });
    
    nikInput.addEventListener('input', function() {
        if (this.value.trim().length >= 16) {
            this.classList.remove('border-red-500');
            this.classList.add('border-green-500');
        } else {
            this.classList.remove('border-green-500');
        }
    });
    
    emailInput.addEventListener('input', function() {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (this.value.trim() === '' || emailRegex.test(this.value)) {
            this.classList.remove('border-red-500');
            this.classList.add('border-green-500');
        } else {
            this.classList.remove('border-green-500');
        }
    });
    
    passwordInput.addEventListener('input', function() {
        if (this.value.length >= 8) {
            this.classList.remove('border-red-500');
            this.classList.add('border-green-500');
        } else {
            this.classList.remove('border-green-500');
        }
    });
    
    // Format nomor HP
    noHpInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, ''); // Hapus karakter non-digit
        
        // Batasi maksimal 15 digit
        if (value.length > 15) {
            value = value.slice(0, 15);
        }
        
        // Format nomor HP Indonesia
        if (value.startsWith('62')) {
            // Jika dimulai dengan 62, biarkan
            this.value = value;
        } else if (value.startsWith('0')) {
            // Jika dimulai dengan 0, biarkan
            this.value = value;
        } else if (value.length > 0) {
            // Jika tidak dimulai dengan 0 atau 62, tambahkan 0
            this.value = '0' + value;
        }
    });
});
</script>
@endpush
@endsection