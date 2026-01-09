@extends('layouts.app')

@section('title', 'Detail User')

@section('header', 'Detail User')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail User</h2>
        <div class="flex space-x-2">
            @if(!$user->trashed())
                <a href="{{ route('superuser.users.edit', $user->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
            @endif
            <a href="{{ route('superuser.users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
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
        <!-- Card Informasi User -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-user mr-2 text-blue-500"></i>
                Informasi User
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">ID User</label>
                    <p class="text-gray-800 font-medium">{{ $user->id }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nama Lengkap</label>
                    <p class="text-gray-800 font-bold text-lg">{{ $user->name }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Username</label>
                    <p class="text-gray-800">{{ $user->username ?? '-' }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                    <p class="text-gray-800">{{ $user->email }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Role</label>
                    <div class="inline-block">
                        <span class="px-3 py-2 text-sm font-medium rounded-lg
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
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Informasi Kontak -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-address-card mr-2 text-green-500"></i>
                Informasi Kontak
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nomor HP</label>
                    <p class="text-gray-800">{{ $user->no_hp ?? '-' }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Alamat</label>
                    <div class="bg-white p-3 rounded-lg border">
                        <p class="text-gray-800">{{ $user->alamat ?? '-' }}</p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Status Akun</label>
                    <div class="inline-block">
                        @if($user->trashed())
                            <span class="px-3 py-2 bg-red-100 text-red-800 rounded-lg text-sm font-medium">
                                <i class="fas fa-user-times mr-1"></i> Nonaktif
                            </span>
                        @else
                            <span class="px-3 py-2 bg-green-100 text-green-800 rounded-lg text-sm font-medium">
                                <i class="fas fa-user-check mr-1"></i> Aktif
                            </span>
                        @endif
                    </div>
                </div>
                
                @if($user->status)
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Status Internal</label>
                    <div class="inline-block">
                        @if($user->status == 'active')
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                <i class="fas fa-check-circle mr-1"></i> Active
                            </span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                <i class="fas fa-times-circle mr-1"></i> Inactive
                            </span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Card Statistik (khusus untuk pelanggan) -->
    @if($user->role === 'pelanggan')
    <div class="mt-6 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-bar mr-2 text-purple-500"></i>
            Statistik Pelanggan
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Meteran</p>
                        <p class="text-2xl font-bold text-blue-600">
                            {{ $user->meterans->count() ?? 0 }}
                        </p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-tachometer-alt text-blue-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Meteran Aktif</p>
                        <p class="text-2xl font-bold text-green-600">
                            {{ $user->meterans->where('status', 'aktif')->count() ?? 0 }}
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
                        <p class="text-sm text-gray-600">Total Tagihan</p>
                        <p class="text-2xl font-bold text-orange-600">
                            {{ $user->meterans->map(function($meteran) { return $meteran->tagihans->count(); })->sum() ?? 0 }}
                        </p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-file-invoice text-orange-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Tagihan Terbayar</p>
                        <p class="text-2xl font-bold text-green-600">
                            {{ $user->meterans->map(function($meteran) { return $meteran->tagihans->where('status', 'sudah_bayar')->count(); })->sum() ?? 0 }}
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-money-check-alt text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Card Data Meteran (khusus untuk pelanggan) -->
    @if($user->role === 'pelanggan' && $user->meterans->count() > 0)
    <div class="mt-6 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-tachometer-alt mr-2 text-indigo-500"></i>
            Data Meteran
        </h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold">Nomor Meteran</th>
                        <th class="py-3 px-4 text-left font-semibold">Status</th>
                        <th class="py-3 px-4 text-left font-semibold">Total Tagihan</th>
                        <th class="py-3 px-4 text-left font-semibold">Terbayar</th>
                        <th class="py-3 px-4 text-left font-semibold">Belum Bayar</th>
                        <th class="py-3 px-4 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($user->meterans as $meteran)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium text-gray-900">{{ $meteran->nomor_meteran }}</td>
                        <td class="py-3 px-4">
                            @if($meteran->status == 'aktif')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-check-circle mr-1"></i> Aktif
                                </span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-times-circle mr-1"></i> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4">{{ $meteran->tagihans->count() }}</td>
                        <td class="py-3 px-4 text-green-600 font-medium">{{ $meteran->tagihans->where('status', 'sudah_bayar')->count() }}</td>
                        <td class="py-3 px-4 text-red-600 font-medium">{{ $meteran->tagihans->where('status', 'belum_bayar')->count() }}</td>
                        <td class="py-3 px-4">
                            <a href="{{ route('superuser.meteran.show', $meteran->id) }}" 
                               class="text-blue-600 hover:text-blue-800" 
                               title="Lihat Detail Meteran">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @if($user->role === 'petugas')
    <div class="mt-6 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-clipboard-list mr-2 text-red-500"></i>
            Statistik Petugas
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Pembacaan</p>
                        <p class="text-2xl font-bold text-blue-600">
                            {{ $user->pembacaanMeteran->count() ?? 0 }}
                        </p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-clipboard-check text-blue-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Pembacaan Bulan Ini</p>
                        <p class="text-2xl font-bold text-green-600">
                            {{ $user->pembacaanMeteran()->whereMonth('created_at', now()->month)->count() ?? 0 }}
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-calendar-alt text-green-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Pembacaan Terakhir</p>
                        <p class="text-lg font-medium text-gray-800">
                            {{ $user->pembacaanMeteran->last() ? $user->pembacaanMeteran->last()->created_at->format('d M Y') : '-' }}
                        </p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-history text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($user->pembacaanMeteran->count() > 0)
    <div class="mt-6 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-tasks mr-2 text-indigo-500"></i>
            Riwayat Pembacaan Terakhir
        </h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold">Tanggal</th>
                        <th class="py-3 px-4 text-left font-semibold">Meteran</th>
                        <th class="py-3 px-4 text-left font-semibold">Pemakaian</th>
                        <th class="py-3 px-4 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($user->pembacaanMeteran->take(5) as $pembacaan)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $pembacaan->created_at->format('d M Y H:i') }}</td>
                        <td class="py-3 px-4 font-medium text-gray-900">
                            {{ $pembacaan->meteran->nomor_meteran ?? '-' }}
                        </td>
                        <td class="py-3 px-4">{{ $pembacaan->pemakaian }} m³</td>
                        <td class="py-3 px-4">
                            @if($pembacaan->status == 'selesai')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-check-circle mr-1"></i> Selesai
                                </span>
                            @else
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-clock mr-1"></i> Proses
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @endif

    <!-- Card Informasi Timestamp -->
    <div class="mt-6 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-clock mr-2 text-purple-500"></i>
            Informasi Waktu
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Dibuat pada</label>
                <p class="text-gray-800">{{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Terakhir diperbarui</label>
                <p class="text-gray-800">{{ $user->updated_at ? $user->updated_at->format('d M Y, H:i') : '-' }}</p>
            </div>

            @if($user->trashed())
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Dinonaktifkan pada</label>
                <p class="text-red-600 font-medium">{{ $user->deleted_at ? $user->deleted_at->format('d M Y, H:i') : '-' }}</p>
            </div>
            @endif

            @if($user->email_verified_at)
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Email Terverifikasi</label>
                <p class="text-green-600">{{ $user->email_verified_at->format('d M Y, H:i') }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex justify-end space-x-2">
        @if($user->trashed())
            <button onclick="openActivateModal({{ $user->id }}, '{{ $user->name }}')"
                    class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-user-check mr-2"></i> Aktifkan User
            </button>
            <button onclick="openDeleteModal({{ $user->id }}, '{{ $user->name }}')"
                    class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-trash-alt mr-2"></i> Hapus Permanen
            </button>
        @else
            <button onclick="openDeactivateModal({{ $user->id }}, '{{ $user->name }}')"
                    class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-user-times mr-2"></i> Nonaktifkan User
            </button>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Function to show notification
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
    
    // Set notification style based on type
    if (type === 'success') {
        notification.classList.add('bg-green-500', 'text-white');
        notification.innerHTML = `<i class="fas fa-check-circle mr-2"></i>${message}`;
    } else if (type === 'error') {
        notification.classList.add('bg-red-500', 'text-white');
        notification.innerHTML = `<i class="fas fa-exclamation-circle mr-2"></i>${message}`;
    } else {
        notification.classList.add('bg-blue-500', 'text-white');
        notification.innerHTML = `<i class="fas fa-info-circle mr-2"></i>${message}`;
    }
    
    // Add to document
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Hide notification after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>
@endpush
@endsection

<!-- Include Modal Components -->
@include('pages.superuser.users.deactivate')
@include('pages.superuser.users.activate')

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Konfirmasi Hapus Permanen</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin menghapus permanen user <strong id="deleteUserName"></strong>?
                </p>
                <p class="text-sm text-red-600 mt-2 font-medium">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Tindakan ini tidak dapat dibatalkan!
                </p>
            </div>
            <div class="flex items-center justify-center gap-3 mt-4">
                <button type="button" 
                        onclick="closeDeleteModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i>Batal
                </button>
                <button type="button" 
                        id="confirmDeleteButton"
                        onclick="confirmDelete()"
                        class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200">
                    <i class="fas fa-trash-alt mr-2"></i>Ya, Hapus Permanen
                </button>
            </div>
        </div>
        <input type="hidden" id="deleteUserId" value="">
    </div>
</div>