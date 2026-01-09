@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')
@section('header', 'Riwayat Pembayaran')
@section('subtitle', 'Riwayat pembayaran pelanggan')

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Pembayaran</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalPembayaran }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-credit-card text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-blue-600 font-medium">
                    {{ $totalPembayaran }} transaksi
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Dibayar</p>
                    <p class="text-3xl font-bold text-green-600">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 font-medium">
                    Total pembayaran sukses
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Pembayaran Pending</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $pembayaranPending }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Menunggu Konfirmasi</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Pembayaran Ditolak</p>
                    <p class="text-3xl font-bold text-red-600">{{ $pembayaranDitolak }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">Perlu Tindakan</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Bulan Ini</p>
                    <p class="text-3xl font-bold text-purple-600">Rp {{ number_format($pembayaranBulanIni, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-purple-600 font-medium">
                    Total pembayaran bulan ini
                </span>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('pelanggan.riwayat.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari keterangan, periode, dll..."
                               class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>

                <!-- Metode Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                    <select name="metode" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                        <option value="">Semua Metode</option>
                        <option value="transfer" {{ request('metode') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="tunai" {{ request('metode') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="e-wallet" {{ request('metode') == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <!-- Tahun Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                    <select name="tahun" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                        <option value="">Semua Tahun</option>
                        @foreach($tahunList as $tahun)
                            <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Bulan Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                    <select name="bulan" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                        <option value="">Semua Bulan</option>
                        @foreach($bulanList as $key => $bulan)
                            <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>{{ $bulan }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Meteran Filter -->
                @if($meteranList && $meteranList->count() > 1)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meteran</label>
                    <select name="meteran_id" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                        <option value="">Semua Meteran</option>
                        @foreach($meteranList as $meteran)
                            <option value="{{ $meteran->id }}" {{ request('meteran_id') == $meteran->id ? 'selected' : '' }}>
                                {{ $meteran->nomor_meteran }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                    <input type="date" 
                           name="tanggal_mulai" 
                           value="{{ request('tanggal_mulai') }}"
                           class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                    <input type="date" 
                           name="tanggal_selesai" 
                           value="{{ request('tanggal_selesai') }}"
                           class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                </div>
            </div>

            <div class="flex items-end space-x-3">
                <button type="submit" 
                        class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors flex items-center">
                    <i class="fas fa-search mr-2"></i>
                    Filter
                </button>
                <a href="{{ route('pelanggan.riwayat.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2.5 rounded-lg font-medium transition-colors flex items-center">
                    <i class="fas fa-undo mr-2"></i>
                    Reset
                </a>

                <!-- Print Button -->
                <button type="button" 
                        onclick="printPreview()"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors flex items-center"
                        title="Preview Cetak">
                    <i class="fas fa-print mr-2"></i>
                    Cetak
                </button>
            </div>
        </form>
    </div>

    <!-- Pembayaran Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Riwayat Pembayaran</h3>
                <div class="text-sm text-gray-500">
                    Total: {{ $pembayaran->total() }} pembayaran
                </div>
            </div>
        </div>

        @if($pembayaran->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meteran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Bayar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pembayaran as $index => $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ($pembayaran->currentPage() - 1) * $pembayaran->perPage() + $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->tagihan->bulan }} {{ $item->tagihan->tahun }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->tagihan->meteran->nomor_meteran }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    Rp {{ number_format($item->jumlah ?? $item->jumlah_bayar ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($item->metode_pembayaran)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            @if($item->metode_pembayaran == 'tunai') bg-green-100 text-green-800
                                            @elseif($item->metode_pembayaran == 'transfer') bg-blue-100 text-blue-800
                                            @else bg-purple-100 text-purple-800 @endif">
                                            {{ ucfirst(str_replace(['-', '_'], ' ', $item->metode_pembayaran)) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($item->tanggal_pembayaran)
                                        {{ \Carbon\Carbon::parse($item->tanggal_pembayaran)->format('d M Y H:i') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($item->status == 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            Pending
                                        </span>
                                    @elseif($item->status == 'lunas')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Lunas
                                        </span>
                                    @elseif($item->status == 'failed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Gagal
                                        </span>
                                    @elseif($item->status == 'cancelled')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-ban mr-1"></i>
                                            Dibatalkan
                                        </span>
                                    @endif

                                    @if($item->status == 'lunas')
                                        @if($item->is_verified)
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    <i class="fas fa-shield-check mr-1"></i>
                                                    Terverifikasi
                                                </span>
                                            </div>
                                        @else
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                                    Belum Verifikasi
                                                </span>
                                            </div>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $item->keterangan ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <!-- Detail Button -->
                                        <a href="{{ route('pelanggan.riwayat.show', $item->id) }}" 
                                           class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded-lg transition-colors"
                                           title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if(in_array($item->status, ['failed', 'cancelled']))
                                            <!-- Resubmit Button -->
                                            <a href="{{ route('pelanggan.riwayat.resubmit', $item->id) }}" 
                                               class="text-orange-600 hover:text-orange-900 p-2 hover:bg-orange-50 rounded-lg transition-colors"
                                               title="Bayar Ulang">
                                                <i class="fas fa-redo"></i>
                                            </a>
                                        @endif

                                        @if($item->status == 'pending')
                                            <!-- Cancel Button -->
                                            <button onclick="confirmCancel({{ $item->id }})" 
                                                    class="text-red-600 hover:text-red-900 p-2 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Batalkan">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $pembayaran->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-history text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada riwayat pembayaran</h3>
                <p class="text-gray-500">Belum ada pembayaran yang dilakukan.</p>
                <div class="mt-4">
                    <a href="{{ route('pelanggan.tagihan.index') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors inline-flex items-center">
                        <i class="fas fa-file-invoice mr-2"></i>
                        Lihat Tagihan
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Cancel Confirmation Modal -->
<div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Batalkan Pembayaran?</h3>
                <button onclick="closeModal('cancelModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <p class="text-sm text-gray-500 text-center">
                    Apakah Anda yakin ingin membatalkan pembayaran ini? Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="flex justify-center gap-4 mt-6">
                    <button onclick="closeModal('cancelModal')" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                    <form id="cancelForm" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Ya, Batalkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 flex items-center">
        <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12 mb-4"></div>
        <div class="ml-4">
            <p id="loadingText" class="text-gray-900 font-medium">Menyiapkan preview cetak...</p>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Cancel payment confirmation
    function confirmCancel(id) {
        document.getElementById('cancelForm').action = "{{ url('pelanggan/riwayat') }}/" + id + "/cancel";
        showModal('cancelModal');
    }

    function printPreview() {
        showLoading('Menyiapkan preview cetak...');
        
        const params = new URLSearchParams(window.location.search);
        if (!params.get('page'));
        const printUrl = `{{ route('pelanggan.riwayat.cetak') }}?${params.toString()}`;
        
        setTimeout(() => {
            window.open(printUrl, '_blank');
            hideLoading();
        }, 500);
    }

    // Modal functions
    function showModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function showLoading(message = 'Memproses...') {
        document.getElementById('loadingText').textContent = message;
        document.getElementById('loadingModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function hideLoading() {
        document.getElementById('loadingModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Close modal when clicking outside
    document.querySelectorAll('[id$="Modal"]').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModals = document.querySelectorAll('[id$="Modal"]:not(.hidden)');
            openModals.forEach(modal => {
                closeModal(modal.id);
            });
        }
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