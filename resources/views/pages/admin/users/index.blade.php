@extends('layouts.app')

@section('title', 'Kelola Pelanggan')
@section('header', 'Kelola Pelanggan')

@section('content')
<div class="bg-white rounded-lg shadow-md">
    <!-- Header Section -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Daftar Pelanggan</h2>
                <p class="text-sm text-gray-600 mt-1">Kelola data pelanggan air bersih</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Search Box -->
                <div class="relative">
                    <input type="text" 
                           id="searchInput"
                           placeholder="Cari pelanggan..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
                <!-- Add Button -->
                <a href="{{ route('admin.users.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Pelanggan
                </a>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="p-6">
        @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
            <p>{{ session('success') }}</p>
        </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg overflow-hidden" id="usersTable">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold">Nama</th>
                        <th class="py-3 px-4 text-left font-semibold">NIK</th>
                        <th class="py-3 px-4 text-left font-semibold">Username</th>
                        <th class="py-3 px-4 text-left font-semibold">Email</th>
                        <th class="py-3 px-4 text-left font-semibold">Role</th>
                        <th class="py-3 px-4 text-left font-semibold">Alamat</th>
                        <th class="py-3 px-4 text-left font-semibold">No HP</th>
                        <th class="py-3 px-4 text-left font-semibold">Status</th>
                        <th class="py-3 px-4 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $user->name }}</td>
                        <td class="py-3 px-4">{{ $user->nik ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $user->username ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $user->email ?? '-' }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($user->role === 'admin')
                                    bg-purple-100 text-purple-800
                                @else
                                    bg-blue-100 text-blue-800
                                @endif">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 max-w-xs truncate" title="{{ $user->alamat ?? '-' }}">
                            {{ $user->alamat ?? '-' }}
                        </td>
                        <td class="py-3 px-4">{{ $user->no_hp ?? '-' }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $user->deleted_at ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ $user->deleted_at ? 'Nonaktif' : 'Aktif' }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.users.show', $user->id) }}" 
                                   class="text-green-600 hover:text-green-800 transition-colors duration-200" 
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                   class="text-blue-600 hover:text-blue-800 transition-colors duration-200" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->deleted_at)
                                <button onclick="openActivateModal({{ $user->id }}, '{{ $user->name }}')" 
                                    class="text-green-500 hover:text-green-700 transition-colors duration-200"
                                    title="Aktifkan">
                                    <i class="fas fa-user-check"></i>
                                </button>
                                @else
                                <button onclick="openDeactivateModal({{ $user->id }}, '{{ $user->name }}')" 
                                    class="text-red-500 hover:text-red-700 transition-colors duration-200"
                                    title="Nonaktifkan">
                                    <i class="fas fa-user-times"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-8 px-4 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gray-100 rounded-full p-4 w-16 h-16 flex items-center justify-center mb-4">
                                    <i class="fas fa-users text-gray-400 text-xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Pelanggan</h3>
                                <p class="text-gray-500 mb-4">Mulai dengan menambahkan pelanggan pertama</p>
                                <a href="{{ route('admin.users.create') }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 inline-flex items-center">
                                    <i class="fas fa-plus mr-2"></i>
                                    Tambah Pelanggan
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Include Modal Deactivate -->
@include('pages.admin.users.deactivate')

<!-- Include Modal Activate -->
@include('pages.admin.users.activate')

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#usersTable tbody tr');
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Auto-hide success message after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('[role="alert"]');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>
@endpush
@endsection