@extends('layouts.app')

@section('title', 'Manajemen Pembayaran')

@section('header', 'Manajemen Pembayaran')

@section('content')
<div class="bg-white rounded-lg shadow-md">
    <!-- Header Section -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Daftar Pembayaran</h2>
                <p class="text-sm text-gray-600 mt-1">Kelola data pembayaran pelanggan</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Search Box -->
                <div class="relative">
                    <input type="text" 
                           id="searchInput"
                           placeholder="Cari pembayaran..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
                <!-- Add Button -->
                <a href="{{ route('admin.pembayaran.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Pembayaran
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="mx-6 mt-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <p>{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mx-6 mt-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <p>{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-2 rounded-lg">
                        <i class="fas fa-credit-card text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Total Pembayaran</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $stats['total_pembayaran'] ?? $pembayarans->count() }}</p>
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
                        <p class="text-lg font-semibold text-gray-800">{{ $stats['total_paid'] ?? $pembayarans->where('status', 'lunas')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="flex items-center">
                    <div class="bg-yellow-100 p-2 rounded-lg">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Pending</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $stats['total_pending'] ?? $pembayarans->where('status', 'pending')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-2 rounded-lg">
                        <i class="fas fa-money-bill-wave text-purple-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Total Nilai</p>
                        <p class="text-lg font-semibold text-gray-800">Rp {{ number_format($stats['total_amount_paid'] ?? $pembayarans->sum('jumlah'), 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <form method="GET" action="{{ route('admin.pembayaran.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Metode</label>
                <select name="metode" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Metode</option>
                    <option value="tunai" {{ request('metode') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="transfer" {{ request('metode') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                    <option value="e-wallet" {{ request('metode') == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex items-end">
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="ml-3 flex space-x-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-filter mr-1"></i>
                        Filter
                    </button>
                    <a href="{{ route('admin.pembayaran.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-times mr-1"></i>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto">
        @if($pembayarans->count() > 0)
            <table class="min-w-full divide-y divide-gray-200" id="pembayaranTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Meteran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Tagihan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Tagihan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Bayar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($pembayarans as $index => $pembayaran)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ ($pembayarans->currentPage() - 1) * $pembayarans->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 rounded-full p-2 mr-3">
                                        <i class="fas fa-user text-blue-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">

                                            @if($pembayaran->tagihan)
                                                @if($pembayaran->tagihan->meteran && $pembayaran->tagihan->meteran->user)
                                                    {{ $pembayaran->tagihan->meteran->user->name ?? $pembayaran->tagihan->meteran->user->nama ?? 'N/A' }}
                                                @elseif($pembayaran->tagihan->pembacaanMeteran && $pembayaran->tagihan->pembacaanMeteran->meteran && $pembayaran->tagihan->pembacaanMeteran->meteran->user)
                                                    {{ $pembayaran->tagihan->pembacaanMeteran->meteran->user->name ?? $pembayaran->tagihan->pembacaanMeteran->meteran->user->nama ?? 'N/A' }}
                                                @else
                                                    <span class="text-gray-400">Data pelanggan tidak tersedia</span>
                                                @endif
                                            @else
                                                <span class="text-gray-400">Tagihan tidak ditemukan</span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500">

                                            @if($pembayaran->tagihan)
                                                @if($pembayaran->tagihan->meteran && $pembayaran->tagihan->meteran->user)
                                                    {{ $pembayaran->tagihan->meteran->user->email ?? 'Email tidak tersedia' }}
                                                @elseif($pembayaran->tagihan->pembacaanMeteran && $pembayaran->tagihan->pembacaanMeteran->meteran && $pembayaran->tagihan->pembacaanMeteran->meteran->user)
                                                    {{ $pembayaran->tagihan->pembacaanMeteran->meteran->user->email ?? 'Email tidak tersedia' }}
                                                @else
                                                    Email tidak tersedia
                                                @endif
                                            @else
                                                Email tidak tersedia
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">

                                @if($pembayaran->tagihan)
                                    @if($pembayaran->tagihan->meteran)
                                        <div class="text-sm text-gray-900">{{ $pembayaran->tagihan->meteran->nomor_meteran ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $pembayaran->tagihan->meteran->alamat ?? '' }}</div>
                                    @elseif($pembayaran->tagihan->pembacaanMeteran && $pembayaran->tagihan->pembacaanMeteran->meteran)
                                        <div class="text-sm text-gray-900">{{ $pembayaran->tagihan->pembacaanMeteran->meteran->nomor_meteran ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $pembayaran->tagihan->pembacaanMeteran->meteran->alamat ?? '' }}</div>
                                    @else
                                        <div class="text-sm text-gray-400">Meteran tidak ditemukan</div>
                                    @endif
                                @else
                                    <div class="text-sm text-gray-400">Data tidak tersedia</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">

                                @if($pembayaran->tagihan && $pembayaran->tagihan->tanggal_tagihan)
                                    {{ \Carbon\Carbon::parse($pembayaran->tagihan->tanggal_tagihan)->format('d M Y') }}
                                @elseif($pembayaran->tagihan && $pembayaran->tagihan->created_at)
                                    {{ $pembayaran->tagihan->created_at->format('d M Y') }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex flex-col">

                                    <span class="font-semibold text-gray-900">
                                        @if($pembayaran->tagihan)
                                            Rp {{ number_format($pembayaran->tagihan->total_tagihan ?? $pembayaran->tagihan->jumlah ?? 0, 0, ',', '.') }}
                                        @else
                                            Rp {{ number_format($pembayaran->jumlah ?? 0, 0, ',', '.') }}
                                        @endif
                                    </span>
                                    <span class="text-xs text-gray-500">

                                        @if($pembayaran->tagihan && isset($pembayaran->tagihan->pemakaian))
                                            {{ number_format($pembayaran->tagihan->pemakaian, 0) }} m³
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">

                                @if($pembayaran->tanggal_pembayaran)
                                    {{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d M Y') }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($pembayaran->metode_pembayaran)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($pembayaran->metode_pembayaran == 'tunai') bg-green-100 text-green-800
                                        @elseif($pembayaran->metode_pembayaran == 'transfer') bg-blue-100 text-blue-800
                                        @else bg-purple-100 text-purple-800 @endif">
                                        @if($pembayaran->metode_pembayaran == 'tunai')
                                            <i class="fas fa-money-bill mr-1"></i>
                                        @elseif($pembayaran->metode_pembayaran == 'transfer')
                                            <i class="fas fa-university mr-1"></i>
                                        @else
                                            <i class="fas fa-mobile-alt mr-1"></i>
                                        @endif
                                        {{ ucfirst(str_replace('-', ' ', $pembayaran->metode_pembayaran)) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($pembayaran->status == 'lunas')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Lunas
                                    </span>
                                @elseif($pembayaran->status == 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Pending
                                    </span>
                                @elseif($pembayaran->status == 'failed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Gagal
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-question-circle mr-1"></i>
                                        {{ ucfirst($pembayaran->status ?? 'Tidak diketahui') }}
                                    </span>
                                @endif

 
                                @if($pembayaran->status == 'lunas' && !$pembayaran->is_verified)
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Belum Verifikasi
                                        </span>
                                    </div>
                                @elseif($pembayaran->is_verified)
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-shield-check mr-1"></i>
                                            Terverifikasi
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <!-- View Button -->
                                    <a href="{{ route('admin.pembayaran.show', $pembayaran->id) }}" 
                                       class="text-gray-600 hover:text-gray-900 transition-colors duration-200"
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <!-- Edit Button -->
                                    <a href="{{ route('admin.pembayaran.edit', $pembayaran->id) }}" 
                                       class="text-green-600 hover:text-green-900 transition-colors duration-200"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- Confirm Button (for unverified payments) -->
                                    @if($pembayaran->status == 'lunas' && !$pembayaran->is_verified)
                                        <form action="{{ route('admin.pembayaran.confirm', $pembayaran->id) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Konfirmasi pembayaran ini?')">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                                    title="Konfirmasi">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <!-- Delete Button -->
                                    <a href="{{ route('admin.pembayaran.delete', $pembayaran->id) }}" 
                                    class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                    title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $pembayarans->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="bg-gray-100 rounded-full p-4 w-16 h-16 mx-auto mb-4">
                    <i class="fas fa-credit-card text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Pembayaran</h3>
                <p class="text-gray-500 mb-6">Mulai dengan menambahkan pembayaran pertama untuk pelanggan.</p>
                <a href="{{ route('admin.pembayaran.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Pembayaran Pertama
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#pembayaranTable tbody tr');
        
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