@extends('layouts.app')

@section('title', 'Detail Pelanggan')
@section('header', 'Detail Pelanggan')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors duration-200 w-fit">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Daftar Pelanggan</span>
        </a>
    </div>

    <!-- User Detail Card -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Informasi Pelanggan</h2>
                
                <!-- Action Buttons -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.users.edit', $user->id) }}" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors duration-200">
                        <i class="fas fa-edit"></i>
                        <span>Edit</span>
                    </a>
                    
                    @if($user->deleted_at)
                    <button onclick="openActivateModal({{ $user->id }}, '{{ $user->name }}')" 
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors duration-200">
                        <i class="fas fa-user-check"></i>
                        <span>Aktifkan</span>
                    </button>
                    @else
                    <button onclick="openDeactivateModal({{ $user->id }}, '{{ $user->name }}')" 
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors duration-200">
                        <i class="fas fa-user-times"></i>Nonaktifkan
                    </button>
                    @endif
                </div>
            </div>

            <!-- User Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                        <i class="fas fa-user text-blue-500 mr-2"></i>
                        Informasi Dasar
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <div class="w-24 text-sm font-medium text-gray-500">Nama:</div>
                            <div class="flex-1 text-sm text-gray-900 font-medium">{{ $user->name }}</div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-24 text-sm font-medium text-gray-500">NIK:</div>
                            <div class="flex-1 text-sm text-gray-900 font-medium">{{ $user->nik ?? '-' }}</div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-24 text-sm font-medium text-gray-500">Username:</div>
                            <div class="flex-1 text-sm text-gray-900">{{ $user->username ?? '-' }}</div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-24 text-sm font-medium text-gray-500">Email:</div>
                            <div class="flex-1 text-sm text-gray-900">
                                @if($user->email)
                                <a href="mailto:{{ $user->email }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $user->email }}
                                </a>
                                @else
                                -
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-24 text-sm font-medium text-gray-500">Role:</div>
                            <div class="flex-1 text-sm text-gray-900">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-24 text-sm font-medium text-gray-500">Status:</div>
                            <div class="flex-1 text-sm text-gray-900">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $user->deleted_at ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $user->deleted_at ? 'Nonaktif' : 'Aktif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                        <i class="fas fa-address-book text-green-500 mr-2"></i>
                        Informasi Kontak
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <div class="w-24 text-sm font-medium text-gray-500">No HP:</div>
                            <div class="flex-1 text-sm text-gray-900">
                                @if($user->no_hp)
                                    <a href="tel:{{ $user->no_hp }}" class="text-green-600 hover:text-green-800">
                                        {{ $user->no_hp }}
                                    </a>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-24 text-sm font-medium text-gray-500">Alamat:</div>
                            <div class="flex-1 text-sm text-gray-900">
                                @if($user->alamat)
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        {{ $user->alamat }}
                                    </div>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Information -->
                <div class="space-y-4 md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                        <i class="fas fa-info-circle text-purple-500 mr-2"></i>
                        Informasi Sistem
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-lg">
                        <div class="text-center">
                            <div class="text-xs font-medium text-gray-500 mb-1">Dibuat</div>
                            <div class="text-sm text-gray-900 font-medium">
                                {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <div class="text-xs font-medium text-gray-500 mb-1">Terakhir Diupdate</div>
                            <div class="text-sm text-gray-900 font-medium">
                                {{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : '-' }}
                            </div>
                        </div>
                        
                        @if($user->deleted_at)
                        <div class="text-center">
                            <div class="text-xs font-medium text-gray-500 mb-1">Dinonaktifkan</div>
                            <div class="text-sm text-red-600 font-medium">
                                {{ $user->deleted_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        @else
                        <div class="text-center">
                            <div class="text-xs font-medium text-gray-500 mb-1">Status Akun</div>
                            <div class="text-sm text-green-600 font-medium">
                                Aktif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openActivateModal(id, name) {
    // Call the function from activate modal
    if (typeof window.openActivateModal === 'function') {
        window.openActivateModal(id, name);
    }
}

function openDeactivateModal(id, name) {
    // Call the function from deactivate modal
    if (typeof window.openDeactivateModal === 'function') {
        window.openDeactivateModal(id, name);
    }
}
</script>
@endpush
@endsection

<!-- Include Modal Deactivate -->
@include('pages.admin.users.deactivate')

<!-- Include Modal Activate -->
@include('pages.admin.users.activate')