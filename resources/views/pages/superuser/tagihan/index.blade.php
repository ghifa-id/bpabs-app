@extends('layouts.app')

@section('title', 'Kelola Tagihan')

@section('header', 'Kelola Tagihan')

@section('content')
<div class="bg-white rounded-lg shadow-md">
    <!-- Header Section -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Daftar Tagihan</h2>
                <p class="text-sm text-gray-600 mt-1">Kelola data tagihan pelanggan</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Search Box -->
                <div class="relative">
                    <input type="text" 
                           id="searchInput"
                           placeholder="Cari tagihan..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
                <!-- Add Button -->
                <a href="{{ route('superuser.tagihan.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Tagihan
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-2 rounded-lg">
                        <i class="fas fa-file-invoice text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Total Tagihan</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $tagihan->total() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="flex items-center">
                    <div class="bg-yellow-100 p-2 rounded-lg">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Belum Bayar</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $tagihan->where('status', 'belum_bayar')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="flex items-center">
                    <div class="bg-green-100 p-2 rounded-lg">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Sudah Bayar</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $tagihan->where('status', 'sudah_bayar')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="flex items-center">
                    <div class="bg-red-100 p-2 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Terlambat</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $tagihan->where('status', 'terlambat')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto">
        @if($tagihan->count() > 0)
            <table class="min-w-full divide-y divide-gray-200" id="tagihanTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Meteran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemakaian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tagihan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jatuh Tempo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($tagihan as $index => $item)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $tagihan->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 rounded-full p-2 mr-3">
                                        <i class="fas fa-user text-blue-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $item->user->name ?? 'N/A' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $item->user->email ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $item->meteran->nomor_meteran ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">{{ $item->meteran->alamat ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->bulan }} {{ $item->tahun }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ number_format($item->jumlah_pemakaian, 0) }} m³</span>
                                    <span class="text-xs text-gray-500">
                                        {{ number_format($item->meter_awal, 0) }} → {{ number_format($item->meter_akhir, 0) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-gray-900">Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}</span>
                                    <span class="text-xs text-gray-500">
                                        @ Rp {{ number_format($item->tarif_per_kubik, 0, ',', '.') }}/m³
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->status == 'belum_bayar')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Belum Bayar
                                    </span>
                                @elseif($item->status == 'sudah_bayar')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>
                                        Sudah Bayar
                                    </span>
                                @elseif($item->status == 'terlambat')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Terlambat
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <!-- Show Button -->
                                    <a href="{{ route('superuser.tagihan.show', $item) }}" 
                                       class="text-green-600 hover:text-green-900 transition-colors duration-200"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <!-- Edit Button -->
                                    <a href="{{ route('superuser.tagihan.edit', $item) }}" 
                                       class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <!-- Delete Button - Link to Delete Confirmation Page -->
                                    <a href="{{ route('superuser.tagihan.delete', $item) }}" 
                                       class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                       title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="bg-gray-100 rounded-full p-4 w-16 h-16 mx-auto mb-4">
                    <i class="fas fa-file-invoice text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Tagihan</h3>
                <p class="text-gray-500 mb-6">Mulai dengan menambahkan tagihan pertama untuk pelanggan.</p>
                <a href="{{ route('superuser.tagihan.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Tagihan Pertama
                </a>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($tagihan->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan {{ $tagihan->firstItem() }} sampai {{ $tagihan->lastItem() }} 
                    dari {{ $tagihan->total() }} hasil
                </div>
                <div class="flex items-center space-x-2">
                    {{ $tagihan->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#tagihanTable tbody tr');
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Auto-hide success/error messages after 5 seconds
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