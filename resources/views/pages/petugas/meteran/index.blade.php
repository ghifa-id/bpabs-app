@extends('layouts.app')

@section('title', 'Pembacaan Meteran')
@section('header', 'Pembacaan Meteran')
@section('subtitle', 'Kelola pembacaan meteran pelanggan')

@section('content')
<div class="bg-white rounded-lg shadow-md">
    <!-- Header Section -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Daftar Meteran</h2>
                <p class="text-sm text-gray-600 mt-1">Kelola pembacaan meteran pelanggan</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Search Box -->
                <div class="relative">
                    <input type="text" 
                           id="searchInput"
                           placeholder="Cari meteran..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
                <!-- Add Button -->
                <a href="{{ route('petugas.meteran.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i>
                    Input Pembacaan
                </a>
            </div>
        </div>
    </div>

    <div class="p-6">
        @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
            <p>{{ session('success') }}</p>
        </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg overflow-hidden" id="meteranTable">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold">No</th>
                        <th class="py-3 px-4 text-left font-semibold">Nomor Meteran</th>
                        <th class="py-3 px-4 text-left font-semibold">Pelanggan</th>
                        <th class="py-3 px-4 text-left font-semibold">Status Pembacaan</th>
                        <th class="py-3 px-4 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($meterans as $index => $meteran)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $index + 1 }}</td>
                        <td class="py-3 px-4 font-medium">{{ $meteran->nomor_meteran }}</td>
                        <td class="py-3 px-4">
                            <div class="flex items-center">
                                <div class="bg-blue-100 rounded-full p-2 mr-3">
                                    <i class="fas fa-user text-blue-600 text-xs"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $meteran->user->name ?? 'N/A' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $meteran->user->email ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            @if($meteran->pembacaanMeteran->count() > 0)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                Sudah Dibaca
                            </span>
                            @else
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                Belum Dibaca
                            </span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('petugas.meteran.show', $meteran->id) }}" 
                                   class="text-blue-600 hover:text-blue-800 transition-colors duration-200" 
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 px-4 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gray-100 rounded-full p-4 w-16 h-16 flex items-center justify-center mb-4">
                                    <i class="fas fa-tachometer-alt text-gray-400 text-xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Meteran</h3>
                                <p class="text-gray-500 mb-4">Tidak ada meteran yang perlu dibaca</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $meterans->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#meteranTable tbody tr');
        
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