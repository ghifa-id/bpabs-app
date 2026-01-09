@extends('layouts.app')

@section('title', 'Edit Pelanggan')
@section('header', 'Edit Pelanggan')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('admin.users.index') }}" class="text-blue-500 hover:text-blue-600 flex items-center gap-2 transition-colors duration-200">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Daftar Pelanggan</span>
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Edit Data Pelanggan</h2>
                    <p class="text-gray-600 mt-1">Ubah informasi pelanggan di bawah ini</p>
                </div>

                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $user->name) }}"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('name') border-red-500 @enderror"
                               placeholder="Masukkan nama lengkap">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NIK Field (readonly) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            NIK
                        </label>
                        <input type="text" 
                               id="nik_display" 
                               value="{{ old('nik', $user->nik) }}"
                               readonly
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed">
                        <input type="hidden" 
                               id="nik" 
                               name="nik" 
                               value="{{ old('nik', $user->nik) }}">
                        @error('nik')
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
                               value="{{ old('username', $user->username) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('username') border-red-500 @enderror"
                               placeholder="Masukkan username">
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
                               value="{{ old('email', $user->email) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('email') border-red-500 @enderror"
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
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('alamat') border-red-500 @enderror"
                                  placeholder="Masukkan alamat lengkap">{{ old('alamat', $user->alamat) }}</textarea>
                        @error('alamat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- No HP Field -->
                    <div>
                        <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-2">
                            No HP
                        </label>
                        <input type="text" 
                               id="no_hp" 
                               name="no_hp" 
                               value="{{ old('no_hp', $user->no_hp) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('no_hp') border-red-500 @enderror"
                               placeholder="Masukkan nomor HP">
                        @error('no_hp')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password Baru
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('password') border-red-500 @enderror"
                               placeholder="Masukkan password baru">
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-info-circle"></i>
                            Kosongkan jika tidak ingin mengubah password
                        </p>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role Field (Hidden for pelanggan) -->
                    <input type="hidden" name="role" value="pelanggan">

                    <!-- Status Display -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Akun</label>
                        <div class="p-3 bg-gray-50 rounded-lg border">
                            <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full
                                {{ $user->deleted_at ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                <i class="fas {{ $user->deleted_at ? 'fa-user-times' : 'fa-user-check' }} mr-2"></i>
                                {{ $user->deleted_at ? 'Nonaktif' : 'Aktif' }}
                            </span>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.users.index') }}" 
                           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-times"></i>
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Additional Info Card -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                <div>
                    <h4 class="text-sm font-medium text-blue-900">Informasi Akun</h4>
                    <div class="text-sm text-blue-700 mt-1 space-y-1">
                        <p><strong>ID Pengguna:</strong> {{ $user->id }}</p>
                        <p><strong>Terdaftar:</strong> {{ $user->created_at->format('d M Y H:i') }}</p>
                        <p><strong>Terakhir Diubah:</strong> {{ $user->updated_at->format('d M Y H:i') }}</p>
                        @if($user->deleted_at)
                            <p><strong>Dinonaktifkan:</strong> {{ $user->deleted_at->format('d M Y H:i') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus on name field
    document.getElementById('name').focus();
    
    // Form elements
    const form = document.querySelector('form');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const noHpInput = document.getElementById('no_hp');
    
    // Form validation
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Reset previous error states
        [nameInput, emailInput, noHpInput].forEach(input => {
            input.classList.remove('border-red-500');
        });
        
        // Validate name
        if (nameInput.value.trim().length < 2) {
            nameInput.classList.add('border-red-500');
            isValid = false;
        }
        
        // Validate email format if provided
        if (emailInput.value.trim() !== '') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailInput.value)) {
                emailInput.classList.add('border-red-500');
                isValid = false;
            }
        }
        
        // Validate phone number format (optional but if filled, should be valid)
        if (noHpInput.value.trim() !== '') {
            const phoneRegex = /^[0-9+\-\s()]{8,15}$/;
            if (!phoneRegex.test(noHpInput.value.trim())) {
                noHpInput.classList.add('border-red-500');
                isValid = false;
            }
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
    
    emailInput.addEventListener('input', function() {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (this.value.trim() === '' || emailRegex.test(this.value)) {
            this.classList.remove('border-red-500');
            this.classList.add('border-green-500');
        } else {
            this.classList.remove('border-green-500');
        }
    });
    
    // Phone number validation
    noHpInput.addEventListener('input', function() {
        if (this.value.trim() === '') {
            this.classList.remove('border-red-500', 'border-green-500');
        } else {
            const phoneRegex = /^[0-9+\-\s()]{8,15}$/;
            if (phoneRegex.test(this.value.trim())) {
                this.classList.remove('border-red-500');
                this.classList.add('border-green-500');
            } else {
                this.classList.remove('border-green-500');
                this.classList.add('border-red-500');
            }
        }
    });
    
    // Auto-format phone number
    noHpInput.addEventListener('input', function() {
        let value = this.value.replace(/[^\d+]/g, '');
        if (value.startsWith('0')) {
            value = '+62' + value.substring(1);
        }
        this.value = value;
    });
});
</script>
@endpush
@endsection