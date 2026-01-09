@extends('layouts.app')

@section('title', 'Kelola User')

@section('header', 'Kelola User')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Daftar User</h2>
        <a href="{{ route('superuser.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
            <i class="fas fa-plus mr-2"></i> Tambah User
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif

    <!-- Search Form -->
    <div class="mb-6">
        <form method="GET" action="{{ route('superuser.users.index') }}" class="flex items-center gap-2">
            <div class="relative flex-grow">
                <input type="text" 
                       name="search" 
                       id="searchInput" 
                       value="{{ $search ?? '' }}"
                       placeholder="Cari nama, email, username, alamat, atau no HP..." 
                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                <i class="fas fa-search"></i>
            </button>
            @if(!empty($search))
            <a href="{{ route('superuser.users.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300"
               title="Clear Search">
                <i class="fas fa-times"></i>
            </a>
            @endif
        </form>
    </div>

    @if(!empty($search))
    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
        <p class="text-sm text-blue-800">
            <i class="fas fa-info-circle mr-1"></i>
            Menampilkan hasil pencarian untuk: <strong>"{{ $search }}"</strong>
            <span class="text-blue-600">({{ $activeUsers->total() + $inactiveUsers->total() }} hasil ditemukan)</span>
        </p>
    </div>
    @endif

        <!-- Active Users Table -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-user-check text-green-500 mr-2"></i>
                User Aktif
                <span class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                    {{ $activeUsers->total() }}
                </span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold">No</th> <!-- Kolom baru untuk nomor urut -->
                        <th class="py-3 px-4 text-left font-semibold">Nama</th>
                        <th class="py-3 px-4 text-left font-semibold">Username</th>
                        <th class="py-3 px-4 text-left font-semibold">Email</th>
                        <th class="py-3 px-4 text-left font-semibold">Role</th>
                        <th class="py-3 px-4 text-left font-semibold">Alamat</th>
                        <th class="py-3 px-4 text-left font-semibold">No HP</th>
                        <th class="py-3 px-4 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($activeUsers as $index => $user) <!-- Tambahkan $index -->
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4">{{ ($activeUsers->currentPage() - 1) * $activeUsers->perPage() + $loop->iteration }}</td> <!-- Nomor urut -->
                        <td class="py-3 px-4 font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="py-3 px-4">{{ $user->username ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $user->email }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
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
                        </td>
                        <td class="py-3 px-4">
                            <div class="max-w-xs truncate" title="{{ $user->alamat ?? '-' }}">
                                {{ $user->alamat ?? '-' }}
                            </div>
                        </td>
                        <td class="py-3 px-4">{{ $user->no_hp ?? '-' }}</td>
                        <td class="py-3 px-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('superuser.users.show', $user->id) }}" 
                                    class="text-green-600 hover:text-green-800" 
                                    title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('superuser.users.edit', $user->id) }}"
                                    class="text-blue-600 hover:text-blue-800"
                                    title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="openDeactivateModal({{ $user->id }}, '{{ $user->name }}')" 
                                    class="text-red-600 hover:text-red-800"
                                    title="Nonaktifkan">
                                    <i class="fas fa-user-times"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-6 px-4 text-center text-gray-500">
                            @if(!empty($search))
                                Tidak ada User aktif yang ditemukan dengan kata kunci "{{ $search }}"
                            @else
                                Belum ada user aktif
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($activeUsers->hasPages())
        <div class="mt-4">
            {{ $activeUsers->appends(['search' => $search, 'inactive_page' => request('inactive_page')])->links() }}
        </div>
        @endif
    </div>

    <!-- Inactive Users Table -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-user-times text-red-500 mr-2"></i>
                User Nonaktif
                <span class="ml-2 bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                    {{ $inactiveUsers->total() }}
                </span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold">No</th> <!-- Kolom baru untuk nomor urut -->
                        <th class="py-3 px-4 text-left font-semibold">Nama</th>
                        <th class="py-3 px-4 text-left font-semibold">Username</th>
                        <th class="py-3 px-4 text-left font-semibold">Email</th>
                        <th class="py-3 px-4 text-left font-semibold">Role</th>
                        <th class="py-3 px-4 text-left font-semibold">Alamat</th>
                        <th class="py-3 px-4 text-left font-semibold">No HP</th>
                        <th class="py-3 px-4 text-left font-semibold">Tanggal Nonaktif</th>
                        <th class="py-3 px-4 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($inactiveUsers as $index => $user) <!-- Tambahkan $index -->
                    <tr class="hover:bg-gray-50 opacity-75">
                        <td class="py-3 px-4">{{ ($inactiveUsers->currentPage() - 1) * $inactiveUsers->perPage() + $loop->iteration }}</td> <!-- Nomor urut -->
                        <td class="py-3 px-4 font-medium text-gray-600">{{ $user->name }}</td>
                        <td class="py-3 px-4">{{ $user->username ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $user->email }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                <i class="fas {{ match($user->role) {
                                    'superuser' => 'fa-crown',
                                    'admin' => 'fa-user-shield',
                                    'pelanggan' => 'fa-user',
                                    default => 'fa-user-question'
                                } }} mr-1"></i>
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="max-w-xs truncate" title="{{ $user->alamat ?? '-' }}">
                                {{ $user->alamat ?? '-' }}
                            </div>
                        </td>
                        <td class="py-3 px-4">{{ $user->no_hp ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $user->deleted_at ? $user->deleted_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="py-3 px-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('superuser.users.show', $user->id) }}" 
                                    class="text-green-600 hover:text-green-800" 
                                    title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <span class="text-gray-400" title="User nonaktif - tidak dapat diedit">
                                    <i class="fas fa-edit"></i>
                                </span>
                                <button onclick="openActivateModal({{ $user->id }}, '{{ $user->name }}')" 
                                    class="text-green-600 hover:text-green-800"
                                    title="Aktifkan">
                                    <i class="fas fa-user-check"></i>
                                </button>
                                <button onclick="openDeleteModal({{ $user->id }}, '{{ $user->name }}')" 
                                    class="text-red-600 hover:text-red-800"
                                    title="Hapus Permanen">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-6 px-4 text-center text-gray-500">
                            @if(!empty($search))
                                Tidak ada User nonaktif yang ditemukan dengan kata kunci "{{ $search }}"
                            @else
                                Tidak ada user yang nonaktif
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($inactiveUsers->hasPages())
        <div class="mt-4">
            {{ $inactiveUsers->appends(['search' => $search, 'page' => request('page')])->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Real-time search functionality
document.getElementById('searchInput').addEventListener('input', function(e) {
    clearTimeout(window.searchTimeout);
    
    window.searchTimeout = setTimeout(() => {
        const searchTerm = e.target.value.trim();
        if (searchTerm.length === 0) {
            return;
        }
    }, 500);
});

// Submit form ketika tekan Enter
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        e.target.closest('form').submit();
    }
});

// Function to open delete confirmation modal
function openDeleteModal(userId, userName) {
    document.getElementById('deleteUserId').value = userId;
    document.getElementById('deleteUserName').textContent = userName;
    document.getElementById('deleteModal').classList.remove('hidden');
}

// Function to close delete modal
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Function to handle permanent delete
function confirmDelete() {
    const userId = document.getElementById('deleteUserId').value;
    const deleteButton = document.getElementById('confirmDeleteButton');
    
    deleteButton.disabled = true;
    deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menghapus...';
    
    fetch(`/superuser/users/${userId}/force-delete`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closeDeleteModal();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Terjadi kesalahan saat menghapus user');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification(error.message || 'Terjadi kesalahan saat menghapus user', 'error');
        deleteButton.disabled = false;
        deleteButton.innerHTML = '<i class="fas fa-trash-alt mr-2"></i>Ya, Hapus Permanen';
    });
}

// Function to show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
    
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
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>
@endpush

<!-- Include Modal Deactivate -->
@include('pages.superuser.users.deactivate')

<!-- Include Modal Activate -->
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
@endsection