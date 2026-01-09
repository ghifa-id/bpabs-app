@extends('layouts.app')

@section('title', 'Laporan Tagihan')
@section('header', 'Laporan Tagihan')
@section('subtitle', 'Laporan data tagihan air sistem BPABS')

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Tagihan</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $statistik['total_tagihan'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-invoice text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-blue-600 font-medium">
                    Rp {{ number_format($statistik['total_nilai_tagihan'] ?? 0, 0, ',', '.') }}
                </span>
                <span class="text-gray-500 ml-1">total nilai</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Sudah Bayar</p>
                    <p class="text-3xl font-bold text-green-600">{{ $statistik['sudah_bayar'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 font-medium">
                    Rp {{ number_format($statistik['nilai_terbayar'] ?? 0, 0, ',', '.') }}
                </span>
                <span class="text-gray-500 ml-1">terbayar</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Belum Bayar</p>
                    <p class="text-3xl font-bold text-red-600">{{ $statistik['belum_bayar'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-red-600 font-medium">
                    Rp {{ number_format($statistik['nilai_belum_bayar'] ?? 0, 0, ',', '.') }}
                </span>
                <span class="text-gray-500 ml-1">outstanding</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Terlambat</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $statistik['terlambat'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Perlu Tindakan</span>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('admin.laporan.tagihan') }}" class="space-y-4">
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
                               placeholder="Cari nama pelanggan, nomor meteran..."
                               class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                        <option value="">Semua Status</option>
                        <option value="belum_bayar" {{ $status == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                        <option value="sudah_bayar" {{ $status == 'sudah_bayar' ? 'selected' : '' }}>Sudah Bayar</option>
                        <option value="terlambat" {{ $status == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    </select>
                </div>

                <!-- Month Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                    <select name="bulan" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                        <option value="">Semua Bulan</option>
                        @php
                        $months = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                        @endphp
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <!-- Year Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                    <select name="tahun" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                        <option value="">Semua Tahun</option>
                        @for($year = date('Y'); $year >= date('Y')-5; $year--)
                            <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                </div>

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
                    <a href="{{ route('admin.laporan.tagihan') }}" 
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

    <!-- Tagihan Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Tagihan</h3>
                <div class="text-sm text-gray-500">
                    Total: {{ $tagihan->total() }} tagihan
                </div>
            </div>
        </div>

        @if($tagihan->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meteran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemakaian</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tagihan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tagihan as $index => $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ($tagihan->currentPage() - 1) * $tagihan->perPage() + $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($item->meteran && $item->meteran->user)
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-semibold text-sm">
                                                    {{ strtoupper(substr($item->meteran->user->name ?? $item->meteran->user->nama ?? 'U', 0, 1)) }}
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $item->meteran->user->name ?? $item->meteran->user->nama ?? 'N/A' }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $item->meteran->user->email }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500">
                                            <i class="fas fa-user-slash mr-2"></i>
                                            Data pelanggan tidak tersedia
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($item->meteran)
                                        <div class="text-sm text-gray-900">
                                            <div class="font-medium">{{ $item->meteran->nomor_meteran }}</div>
                                            <div class="text-gray-500 text-xs">
                                                Status: {{ ucfirst($item->meteran->status ?? 'N/A') }}
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">Meteran tidak tersedia</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <div class="font-medium">
                                            @if($item->bulan && $item->bulan >= 1 && $item->bulan <= 12)
                                                {{ DateTime::createFromFormat('!m', $item->bulan)->format('M') }} {{ $item->tahun }}
                                            @else
                                                {{ $item->bulan ?? '-' }}/{{ $item->tahun ?? '-' }}
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500">Periode tagihan</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if(isset($item->pemakaian))
                                            <div class="font-medium">{{ number_format($item->pemakaian, 0, ',', '.') }} m³</div>
                                            <div class="text-xs text-gray-500">Volume air</div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-lg font-bold text-gray-900">
                                        Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}
                                    </div>
                                    @if(isset($item->denda) && $item->denda > 0)
                                        <div class="text-xs text-red-600">
                                            + Denda: Rp {{ number_format($item->denda, 0, ',', '.') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($item->status)
                                        @case('sudah_bayar')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Sudah Bayar
                                            </span>
                                            @break
                                        @case('terlambat')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>
                                                Terlambat
                                            </span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-circle mr-1"></i>
                                                Belum Bayar
                                            </span>
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="showDetailTagihan({{ $item->id }})" 
                                                class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded-lg transition-colors"
                                                title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($item->status !== 'sudah_bayar')
                                            <a href="{{ route('admin.tagihan.edit', $item->id) }}" 
                                               class="text-indigo-600 hover:text-indigo-900 p-2 hover:bg-indigo-50 rounded-lg transition-colors"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
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
                {{ $tagihan->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-file-invoice text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada tagihan ditemukan</h3>
                <p class="text-gray-500">Tidak ada tagihan yang sesuai dengan filter yang dipilih.</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal Detail Tagihan -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Detail Tagihan</h3>
                <button onclick="closeModal('detailModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="detailContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Pembayaran -->
<div id="pembayaranModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
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
    let currentTagihanId = null;
    let currentPembayaranId = null;


    function showDetailTagihan(id) {
        currentTagihanId = id;
        showLoading('Memuat detail tagihan...');
        
        fetch(`{{ url('admin/tagihan') }}/${id}/detail`, {
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
            if (data.tagihan) {
                const tagihan = data.tagihan;
                const meteran = tagihan.meteran;
                const user = meteran ? meteran.user : null;
                
                document.getElementById('detailContent').innerHTML = `
                    <div class="space-y-6">
                        <!-- Tagihan Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Informasi Tagihan</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">ID Tagihan</label>
                                        <p class="text-sm text-gray-900 font-mono">#${tagihan.id}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Periode</label>
                                        <p class="text-sm text-gray-900">${getMonthName(tagihan.bulan)} ${tagihan.tahun}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Status</label>
                                        <p class="text-sm ${getStatusColor(tagihan.status)}">${getStatusText(tagihan.status)}</p>
                                    </div>
                                    ${tagihan.pemakaian ? `
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Pemakaian Air</label>
                                            <p class="text-sm text-gray-900">${new Intl.NumberFormat('id-ID').format(tagihan.pemakaian)} m³</p>
                                        </div>
                                    ` : ''}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Total Tagihan</label>
                                        <p class="text-lg font-bold text-gray-900">Rp ${new Intl.NumberFormat('id-ID').format(tagihan.total_tagihan)}</p>
                                    </div>
                                    ${tagihan.denda && tagihan.denda > 0 ? `
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Denda</label>
                                            <p class="text-sm text-red-600">Rp ${new Intl.NumberFormat('id-ID').format(tagihan.denda)}</p>
                                        </div>
                                    ` : ''}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tanggal Dibuat</label>
                                        <p class="text-sm text-gray-900">${new Date(tagihan.created_at).toLocaleDateString('id-ID')}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Pelanggan & Meteran Info -->
                            <div class="space-y-4">
                                <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Informasi Pelanggan & Meteran</h4>
                                <div class="space-y-3">
                                    ${user ? `
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Nama Pelanggan</label>
                                            <p class="text-sm text-gray-900">${user.name || user.nama || 'N/A'}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Email</label>
                                            <p class="text-sm text-gray-900">${user.email}</p>
                                        </div>
                                        ${user.telepon || user.no_hp ? `
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Telepon</label>
                                                <p class="text-sm text-gray-900">${user.telepon || user.no_hp}</p>
                                            </div>
                                        ` : ''}
                                    ` : ''}
                                    ${meteran ? `
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Nomor Meteran</label>
                                            <p class="text-sm text-gray-900 font-mono">${meteran.nomor_meteran}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Status Meteran</label>
                                            <p class="text-sm text-gray-900">${meteran.status}</p>
                                        </div>
                                        ${meteran.alamat ? `
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Alamat Meteran</label>
                                                <p class="text-sm text-gray-900">${meteran.alamat}</p>
                                            </div>
                                        ` : ''}
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Riwayat Pembayaran -->
                        ${tagihan.pembayaran && tagihan.pembayaran.length > 0 ? `
                            <div class="border-t pt-6">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Pembayaran</h4>
                                <div class="space-y-3">
                                    ${tagihan.pembayaran.map(pembayaran => `
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <p class="font-medium text-gray-900">
                                                        Rp ${new Intl.NumberFormat('id-ID').format(pembayaran.jumlah_bayar || pembayaran.jumlah)}
                                                    </p>
                                                    <p class="text-sm text-gray-600">
                                                        ${pembayaran.metode_pembayaran} • ${new Date(pembayaran.tanggal_pembayaran).toLocaleDateString('id-ID')}
                                                    </p>
                                                </div>
                                                <span class="text-xs px-2 py-1 rounded-full ${pembayaran.status === 'lunas' || pembayaran.status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                                                    ${pembayaran.status}
                                                </span>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        ` : ''}
                        
                        <!-- Actions -->
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            ${tagihan.status !== 'sudah_bayar' ? `
                                <a href="{{ url('admin/tagihan') }}/${tagihan.id}/edit" 
                                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                                    <i class="fas fa-edit mr-2"></i>
                                    Edit Tagihan
                                </a>
                            ` : ''}
                            ${tagihan.pembayaran && tagihan.pembayaran.length > 0 ? `
                                <button onclick="viewPembayaran(${tagihan.pembayaran[0].id})" 
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                                    <i class="fas fa-receipt mr-2"></i>
                                    Lihat Pembayaran
                                </button>
                            ` : ''}
                        </div>
                    </div>
                `;
                
                showModal('detailModal');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showToast('Terjadi kesalahan saat memuat detail tagihan', 'error');
        });
    }

    // View pembayaran detail
    function viewPembayaran(id) {
        currentPembayaranId = id;
        showLoading('Memuat detail pembayaran...');
        
        fetch(`{{ url('admin/pembayaran') }}/${id}`, {
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
                                        <p class="text-lg font-bold text-green-600">Rp ${new Intl.NumberFormat('id-ID').format(pembayaran.jumlah_bayar || pembayaran.jumlah)}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                                        <p class="text-sm text-gray-900">${pembayaran.metode_pembayaran}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Status</label>
                                        <p class="text-sm ${pembayaran.status === 'lunas' || pembayaran.status === 'paid' ? 'text-green-600' : pembayaran.status === 'pending' ? 'text-yellow-600' : 'text-red-600'}">
                                            ${pembayaran.status.toUpperCase()}
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

    // Helper functions
    function getMonthName(monthNumber) {
        const months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        // Validasi input
        if (!monthNumber || monthNumber < 1 || monthNumber > 12) {
            return monthNumber || '-';
        }
        
        return months[monthNumber - 1] || monthNumber;
    }

    function getStatusText(status) {
        const statusMap = {
            'sudah_bayar': 'Sudah Bayar',
            'belum_bayar': 'Belum Bayar',
            'terlambat': 'Terlambat'
        };
        return statusMap[status] || status;
    }

    function getStatusColor(status) {
        const colorMap = {
            'sudah_bayar': 'text-green-600',
            'belum_bayar': 'text-red-600',
            'terlambat': 'text-yellow-600'
        };
        return colorMap[status] || 'text-gray-600';
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


    function printPreview() {
        showLoading('Menyiapkan preview cetak...');
        
        const params = new URLSearchParams(window.location.search);
        const printUrl = `{{ route('admin.laporan.tagihan.cetak') }}?${params.toString()}`;
        
        setTimeout(() => {
            window.open(printUrl, '_blank');
            hideLoading();
        }, 1000);
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