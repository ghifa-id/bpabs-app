@extends('layouts.app')

@section('title', 'Edit User')

@section('header', 'Edit User')

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

    <form action="{{ route('superuser.users.update', $user->id) }}" method="POST" class="space-y-6">
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
                   value="{{ old('username', $user->username) }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('username') border-red-500 @enderror"
                   placeholder="Masukkan username">
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
                   value="{{ old('email', $user->email) }}"
                   required
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                   placeholder="contoh@example.com">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Role Field (Read-only) -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Role
            </label>
            <div class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-50 flex items-center justify-between">
                <span class="px-2 py-1 text-xs font-medium rounded-full
                    {{ match($user->role) {
                        'superuser' => 'bg-purple-100 text-purple-800',
                        'admin' => 'bg-blue-100 text-blue-800',
                        'pelanggan' => 'bg-green-100 text-green-800',
                        'petugas' => 'bg-red-100 text-red-800',
                        default => 'bg-gray-100 text-gray-800'
                    } }}">
                    <i class="fas {{ match($user->role) {
                        'superuser' => 'fa-crown',
                        'admin' => 'fa-user-shield',
                        'pelanggan' => 'fa-user',
                        'petugas' => 'fa-user-tie',
                        default => 'fa-user-question'
                    } }} mr-1"></i>
                    {{ ucfirst($user->role) }}
                </span>
                <span class="text-gray-400 text-sm">
                    <i class="fas fa-lock mr-1"></i>Tidak dapat diubah
                </span>
            </div>
            <input type="hidden" name="role" value="{{ $user->role }}">
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
                      placeholder="Masukkan alamat lengkap">{{ old('alamat', $user->alamat) }}</textarea>
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
                   value="{{ old('no_hp', $user->no_hp) }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('no_hp') border-red-500 @enderror"
                   placeholder="Contoh: 08123456789">
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
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                   placeholder="Masukkan password baru">
            <p class="mt-1 text-xs text-gray-500">
                <i class="fas fa-info-circle mr-1"></i>
                Kosongkan jika tidak ingin mengubah password
            </p>
            @error('password')
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
                           {{ old('status', $user->status) == 'active' && !$user->deleted_at ? 'checked' : '' }}
                           {{ $user->deleted_at ? 'disabled' : '' }}>
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
                           {{ old('status', $user->status) == 'inactive' || $user->deleted_at ? 'checked' : '' }}
                           {{ $user->deleted_at ? 'disabled' : '' }}>
                    <label for="status_inactive" class="ml-2 block text-sm text-gray-700">
                        Nonaktif
                    </label>
                </div>
            </div>
            @if($user->deleted_at)
                <p class="mt-1 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Status akun tidak dapat diubah karena user dinonaktifkan
                </p>
            @endif
        </div>

        <!-- Account Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-blue-800 mb-2">
                <i class="fas fa-info-circle mr-1"></i> Informasi Akun
            </h4>
            <div class="text-sm text-blue-700">
                <p><strong>ID:</strong> {{ $user->id }}</p>
                <p><strong>Terdaftar:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Terakhir Diubah:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</p>
                @if($user->deleted_at)
                    <p><strong>Dinonaktifkan:</strong> {{ $user->deleted_at->format('d/m/Y H:i') }}</p>
                @endif
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                <i class="fas fa-save mr-2"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus on name field
    document.getElementById('name').focus();

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