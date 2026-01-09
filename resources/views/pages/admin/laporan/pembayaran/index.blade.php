@extends('layouts.app')

@section('title', 'Laporan Pembayaran')
@section('header', 'Laporan Pembayaran')
@section('subtitle', 'Laporan data pembayaran sistem BPABS')

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Pembayaran</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $statistik['total_pembayaran'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-invoice text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-blue-600 font-medium">
                    {{ $statistik['total_pembayaran'] ?? 0 }} transaksi
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Pembayaran Sukses</p>
                    <p class="text-3xl font-bold text-green-600">{{ $statistik['pembayaran_lunas'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 font-medium">
                    Rp {{ number_format($statistik['total_nilai_pembayaran'] ?? 0, 0, ',', '.') }}
                </span>
                <span class="text-gray-500 ml-1">terbayar</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Pembayaran Pending</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $statistik['pembayaran_pending'] ?? 0 }}</p>
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
                    <p class="text-sm text-gray-600 font-medium">Pembayaran Gagal</p>
                    <p class="text-3xl font-bold text-red-600">{{ $statistik['pembayaran_gagal'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">Perlu Tindakan</span>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('admin.laporan.pembayaran') }}" class="space-y-4">
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
                               value="{{ $search }}"
                               placeholder="Cari nama pelanggan, order ID, transaction ID..."
                               class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                        <option value="">Semua Status</option>
                        <option value="lunas" {{ $status == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="failed" {{ $status == 'failed' ? 'selected' : '' }}>Gagal</option>
                    </select>
                </div>

                <!-- Metode Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Metode</label>
                    <select name="metode" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                        <option value="">Semua Metode</option>
                        @foreach($metode_pembayaran_list as $metode_item)
                            <option value="{{ $metode_item }}" {{ $metode_item == $metode ? 'selected' : '' }}>
                                {{ ucfirst($metode_item) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                    <input type="date" 
                           name="tanggal_dari" 
                           value="{{ $tanggal_dari }}"
                           class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                    <input type="date" 
                           name="tanggal_sampai" 
                           value="{{ $tanggal_sampai }}"
                           class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                </div>

                <div class="flex items-end space-x-3">
                    <button type="submit" 
                            class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('admin.laporan.pembayaran') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2.5 rounded-lg font-medium transition-colors flex items-center">
                        <i class="fas fa-undo mr-2"></i>
                        Reset
                    </a>
                </div>

                <div class="flex items-end justify-end space-x-3">
                    <!-- Print Button -->
                    <button type="button" 
                            onclick="printPreview()"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors flex items-center"
                            title="Preview Cetak">
                        <i class="fas fa-print mr-2"></i>
                        Cetak
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Pembayaran Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Pembayaran</h3>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Meteran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Tagihan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Bayar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pembayaran as $index => $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ($pembayaran->currentPage() - 1) * $pembayaran->perPage() + $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($item->tagihan && $item->tagihan->meteran && $item->tagihan->meteran->user)
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-semibold text-sm">
                                                    {{ strtoupper(substr($item->tagihan->meteran->user->name ?? $item->tagihan->meteran->user->nama ?? 'U', 0, 1)) }}
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $item->tagihan->meteran->user->name ?? $item->tagihan->meteran->user->nama ?? 'N/A' }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $item->tagihan->meteran->user->email }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500">
                                            <i class="fas fa-user-slash mr-2"></i>
                                            Data pelanggan tidak tersedia
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($item->tagihan && $item->tagihan->meteran)
                                        {{ $item->tagihan->meteran->nomor_meteran ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->order_id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($item->tagihan)
                                        {{ \Carbon\Carbon::parse($item->tagihan->tanggal_tagihan ?? $item->tagihan->created_at)->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($item->tanggal_pembayaran)->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ucfirst($item->metode_pembayaran) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-lg font-bold text-gray-900">
                                            Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}
                                        </span>
                                        @if($item->tagihan && isset($item->tagihan->pemakaian))
                                            <span class="text-xs text-gray-500">
                                                {{ number_format($item->tagihan->pemakaian, 0) }} m³
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($item->status)
                                        @case('lunas')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Lunas
                                            </span>
                                            @break
                                        @case('pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>
                                                Pending
                                            </span>
                                            @break
                                        @case('failed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle mr-1"></i>
                                                Gagal
                                            </span>
                                            @break
                                    @endswitch
                                    
                                    {{-- Tampilkan status verifikasi jika lunas --}}
                                    @if($item->status == 'lunas')
                                        <div class="mt-1">
                                            @if($item->is_verified)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    <i class="fas fa-shield-check mr-1"></i>
                                                    Terverifikasi
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                                    Belum Verifikasi
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="showDetailPembayaran({{ $item->id }})" 
                                                class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded-lg transition-colors"
                                                title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($item->status == 'lunas' && !$item->is_verified)
                                            <button onclick="verifikasiPembayaran({{ $item->id }})" 
                                                    class="text-green-600 hover:text-green-900 p-2 hover:bg-green-50 rounded-lg transition-colors"
                                                    title="Verifikasi">
                                                <i class="fas fa-check-double"></i>
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
                {{ $pembayaran->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-file-invoice text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada pembayaran ditemukan</h3>
                <p class="text-gray-500">Tidak ada pembayaran yang sesuai dengan filter yang dipilih.</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal Detail Pembayaran -->
<div id="pembayaranModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Detail Pembayaran</h3>
                <button onclick="closeModal('pembayaranModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="pembayaranContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl p-8 max-w-sm w-full text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto mb-4"></div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Memproses...</h3>
        <p class="text-gray-500" id="loadingText">Sedang menyiapkan laporan</p>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentPembayaranId = null;
    
    function showDetailPembayaran(id) {
        currentPembayaranId = id;
        showLoading('Memuat detail pembayaran...');
        
        fetch(`{{ url('admin/pembayaran') }}/${id}/detail`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.pembayaran) {
                const pembayaran = data.pembayaran;
                const tagihan = pembayaran.tagihan;
                
                document.getElementById('pembayaranContent').innerHTML = `
                    <div class="space-y-6">
                        <!-- Pembayaran Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Informasi Pembayaran</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Order ID</label>
                                        <p class="text-sm text-gray-900 font-mono">${pembayaran.order_id}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Transaction ID</label>
                                        <p class="text-sm text-gray-900 font-mono">${pembayaran.transaction_id || '-'}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                                        <p class="text-lg font-bold text-green-600">Rp ${new Intl.NumberFormat('id-ID').format(pembayaran.jumlah_bayar)}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                                        <p class="text-sm text-gray-900">${pembayaran.metode_pembayaran}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Status</label>
                                        <p class="text-sm ${pembayaran.status === 'lunas' ? 'text-green-600' : pembayaran.status === 'pending' ? 'text-yellow-600' : 'text-red-600'}">
                                            ${pembayaran.status.toUpperCase()}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Status Verifikasi</label>
                                        <p class="text-sm ${pembayaran.is_verified ? 'text-green-600' : 'text-yellow-600'}">
                                            ${pembayaran.is_verified ? 'Terverifikasi' : 'Belum Diverifikasi'}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tanggal Pembayaran</label>
                                        <p class="text-sm text-gray-900">${new Date(pembayaran.tanggal_pembayaran).toLocaleDateString('id-ID', { 
                                            weekday: 'long', 
                                            year: 'numeric', 
                                            month: 'long', 
                                            day: 'numeric',
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        })}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tagihan Info -->
                            <div class="space-y-4">
                                <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Informasi Tagihan</h4>
                                <div class="space-y-3">
                                    ${tagihan ? `
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">ID Tagihan</label>
                                            <p class="text-sm text-gray-900">#${tagihan.id}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Periode</label>
                                            <p class="text-sm text-gray-900">${getMonthName(tagihan.bulan)} ${tagihan.tahun}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Total Tagihan</label>
                                            <p class="text-sm text-gray-900">Rp ${new Intl.NumberFormat('id-ID').format(tagihan.total_tagihan)}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Pemakaian</label>
                                            <p class="text-sm text-gray-900">${tagihan.pemakaian || 0} m³</p>
                                        </div>
                                        ${tagihan.meteran ? `
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Nomor Meteran</label>
                                                <p class="text-sm text-gray-900">${tagihan.meteran.nomor_meteran}</p>
                                            </div>
                                        ` : ''}
                                    ` : '<p class="text-gray-500">Informasi tagihan tidak tersedia</p>'}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Details -->
                        ${pembayaran.payment_response ? `
                            <div class="border-t pt-6">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Detail Response Payment Gateway</h4>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <pre class="text-sm text-gray-700 whitespace-pre-wrap">${JSON.stringify(JSON.parse(pembayaran.payment_response), null, 2)}</pre>
                                </div>
                            </div>
                        ` : ''}
                        
                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            ${pembayaran.status === 'lunas' && !pembayaran.is_verified ? `
                                <button onclick="verifikasiPembayaran(${pembayaran.id})" 
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                                    <i class="fas fa-check-double mr-2"></i>
                                    Verifikasi Pembayaran
                                </button>
                            ` : ''}
                        </div>
                    </div>
                `;
                
                showModal('pembayaranModal');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showToast('Terjadi kesalahan saat memuat detail pembayaran', 'error');
        });
    }

    function verifikasiPembayaran(id) {
        if (confirm('Apakah Anda yakin ingin memverifikasi pembayaran ini?')) {
            showLoading('Memverifikasi pembayaran...');
            
            fetch(`{{ url('admin/pembayaran') }}/${id}/verifikasi`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showToast(data.message, 'success');
                    location.reload();
                } else {
                    showToast('Gagal memverifikasi pembayaran: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showToast('Terjadi kesalahan saat memverifikasi pembayaran', 'error');
            });
        }
    }

    function printPreview() {
        showLoading('Menyiapkan preview cetak...');
        
        const params = new URLSearchParams(window.location.search);
        const printUrl = `{{ route('admin.laporan.pembayaran.cetak') }}?${params.toString()}`;
        
        setTimeout(() => {
            window.open(printUrl, '_blank');
            hideLoading();
        }, 1000);
    }

    // Helper functions
    function getMonthName(monthNumber) {
        const months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        if (!monthNumber || monthNumber < 1 || monthNumber > 12) {
            return monthNumber || '-';
        }
        
        return months[monthNumber - 1] || monthNumber;
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

    function showToast(message, type = 'info') {
        // Remove existing toasts
        const existingToasts = document.querySelectorAll('.toast-notification');
        existingToasts.forEach(toast => toast.remove());
        
        // Create toast
        const toast = document.createElement('div');
        toast.className = `toast-notification fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 translate-x-full`;
        
        // Set colors based on type
        const colors = {
            success: 'bg-green-50 border border-green-200 text-green-800',
            error: 'bg-red-50 border border-red-200 text-red-800',
            warning: 'bg-yellow-50 border border-yellow-200 text-yellow-800',
            info: 'bg-blue-50 border border-blue-200 text-blue-800'
        };
        
        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };
        
        toast.className += ` ${colors[type] || colors.info}`;
        
        toast.innerHTML = `
            <div class="flex items-center">
                <i class="${icons[type] || icons.info} mr-3"></i>
                <span class="flex-1">${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Trigger animation
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 10);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 300);
        }, 5000);
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
        // Escape key to close modals
        if (e.key === 'Escape') {
            const openModals = document.querySelectorAll('[id$="Modal"]:not(.hidden)');
            openModals.forEach(modal => {
                closeModal(modal.id);
            });
        }
        
        // Ctrl+P for print preview
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            printPreview();
        }
    });

    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('[role="alert"]');
        alerts.forEach(alert => {
            setTimeout(() => {
                if (alert && alert.parentNode) {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.remove();
                        }
                    }, 300);
                }
            }, 5000);
        });
    });
</script>
@endpush