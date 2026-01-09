@extends('layouts.app')

@section('title', 'Tagihan Saya')

@section('header', 'Tagihan Saya')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-file-invoice text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Tagihan</p>
                    <p class="text-2xl font-bold text-gray-800" id="stat-total-tagihan">{{ $totalTagihan ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Belum Bayar</p>
                    <p class="text-2xl font-bold text-yellow-600" id="stat-belum-bayar">{{ $belumBayar ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="bg-red-100 p-3 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Terlambat</p>
                    <p class="text-2xl font-bold text-red-600" id="stat-terlambat">{{ $terlambat ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Sudah Bayar</p>
                    <p class="text-2xl font-bold text-green-600" id="stat-sudah-bayar">{{ $sudahBayar ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="bg-red-100 p-3 rounded-lg">
                    <i class="fas fa-money-bill-wave text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Hutang</p>
                    <p class="text-lg font-bold" id="stat-total-hutang">
                        @if(($totalHutang ?? 0) > 0)
                            <span class="text-red-600">Rp {{ number_format($totalHutang, 0, ',', '.') }}</span>
                        @else
                            <span class="text-green-600">Lunas</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- TAMBAHAN: Info Card untuk Menunggu Konfirmasi jika ada -->
    @if(isset($menungguKonfirmasi) && $menungguKonfirmasi > 0)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 pending-notification">
        <div class="flex items-center">
            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
            <span class="text-blue-800">
                Anda memiliki <strong>{{ $menungguKonfirmasi }}</strong> tagihan yang sedang menunggu konfirmasi pembayaran.
                Status akan diperbarui otomatis setelah konfirmasi dari gateway pembayaran.
            </span>
            <button onclick="refreshPaymentStatus()" class="ml-auto bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                <i class="fas fa-sync-alt mr-1"></i> Refresh
            </button>
        </div>
    </div>
    @endif

    <!-- Main Content -->
    <div class="bg-white rounded-lg shadow-md">
        <!-- Header Section -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Riwayat Tagihan</h2>
                    <p class="text-sm text-gray-600 mt-1">Kelola dan pantau tagihan air Anda</p>
                </div>
                
                <!-- Filters -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <form method="GET" class="flex flex-col sm:flex-row gap-3">
                        <!-- Search -->
                        <div class="relative">
                            <input type="text" 
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Cari tagihan..." 
                                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-48">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                        
                        <!-- Status Filter -->
                        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua Status</option>
                            <option value="belum_bayar" {{ request('status') == \App\Models\Tagihan::STATUS_BELUM_BAYAR ? 'selected' : '' }}>Belum Bayar</option>
                            <option value="sudah_bayar" {{ request('status') == \App\Models\Tagihan::STATUS_SUDAH_BAYAR ? 'selected' : '' }}>Sudah Bayar</option>
                            <option value="terlambat" {{ request('status') == \App\Models\Tagihan::STATUS_TERLAMBAT ? 'selected' : '' }}>Terlambat</option>
                            <option value="menunggu_konfirmasi" {{ request('status') == \App\Models\Tagihan::STATUS_MENUNGGU_KONFIRMASI ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                        </select>
                        
                        <!-- Year Filter -->
                        <select name="tahun" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua Tahun</option>
                            @foreach($tahunList as $tahun)
                                <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                            @endforeach
                        </select>
                        
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        
                        @if(request()->hasAny(['search', 'status', 'tahun']))
                            <a href="{{ route('pelanggan.tagihan.index') }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                                <i class="fas fa-times mr-1"></i> Reset
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto">
            @if($tagihan->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Meteran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemakaian</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tagihan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jatuh Tempo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tagihan as $index => $item)
                            @php
                                // Gunakan status dari database yang sudah disinkronisasi oleh controller
                                $actualStatus = $item->status;
                                $pembayaran = $item->pembayaran;
                                
                                // Cek apakah terlambat berdasarkan tanggal jatuh tempo
                                $jatuhTempo = $item->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($item->tanggal_jatuh_tempo) : null;
                                $isOverdue = $jatuhTempo && $jatuhTempo->isPast() && !in_array($actualStatus, ['sudah_bayar', 'menunggu_konfirmasi']);
                                
                                // Tentukan class CSS untuk styling
                                $rowClass = '';
                                if ($isOverdue || $actualStatus == \App\Models\Tagihan::STATUS_TERLAMBAT) {
                                    $rowClass = 'bg-red-50 hover:bg-red-100';
                                } elseif ($actualStatus == \App\Models\Tagihan::STATUS_MENUNGGU_KONFIRMASI) {
                                    $rowClass = 'bg-blue-50 hover:bg-blue-100';
                                } elseif ($actualStatus == \App\Models\Tagihan::STATUS_SUDAH_BAYAR) {
                                    $rowClass = 'bg-green-50 hover:bg-green-100';
                                }
                            @endphp
                            
                            <tr class="transition-colors duration-200 {{ $rowClass }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $tagihan->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->bulan }} {{ $item->tahun }}</div>
                                    <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($item->tanggal_tagihan)->format('d M Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $item->meteran->nomor_meteran ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($item->meteran->alamat ?? '', 30) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-blue-600">
                                            {{ number_format($item->pemakaian ?? 0, 0) }} m³
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ number_format($item->meter_awal ?? 0, 0) }} → 
                                            {{ number_format($item->meter_akhir ?? 0, 0) }}
                                        </span>
                                    </div>
                                </td>
                               <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-900">Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}</span>
                                        <span class="text-xs text-gray-500">
                                            @ Rp {{ number_format($item->tarif_per_m3 ?? 0, 0, ',', '.') }}/m³
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <!-- Status badges berdasarkan status aktual dari database -->
                                    @if($actualStatus == \App\Models\Tagihan::STATUS_BELUM_BAYAR)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            Belum Bayar
                                        </span>
                                    @elseif($actualStatus == \App\Models\Tagihan::STATUS_SUDAH_BAYAR)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i>
                                            Sudah Bayar
                                        </span>
                                    @elseif($actualStatus == \App\Models\Tagihan::STATUS_TERLAMBAT)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Terlambat
                                        </span>
                                    @elseif($actualStatus == \App\Models\Tagihan::STATUS_MENUNGGU_KONFIRMASI)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 status-pending">
                                            <i class="fas fa-hourglass-half mr-1"></i>
                                            Menunggu Konfirmasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-question mr-1"></i>
                                            {{ ucfirst(str_replace('_', ' ', $actualStatus)) }}
                                        </span>
                                    @endif
                                    
                                    <!-- Info pembayaran jika ada -->
                                    @if($pembayaran)
                                        <div class="text-xs text-gray-500 mt-1">
                                            <div>{{ ucfirst($pembayaran->metode_pembayaran ?? 'N/A') }}</div>
                                            @if($pembayaran->tanggal_pembayaran)
                                                <div>{{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d/m/Y H:i') }}</div>
                                            @endif
                                            @if($pembayaran->transaction_id)
                                                <div class="text-blue-600">ID: {{ substr($pembayaran->transaction_id, 0, 8) }}...</div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($jatuhTempo)
                                        <div class="text-sm {{ $isOverdue ? 'text-red-600 font-medium' : 'text-gray-900' }}">
                                            {{ $jatuhTempo->format('d/m/Y') }}
                                        </div>
                                        @if($isOverdue && $actualStatus !== \App\Models\Tagihan::STATUS_BELUM_BAYAR)
                                            <div class="text-xs text-red-500 font-medium">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                Terlambat {{ $jatuhTempo->diffForHumans() }}
                                            </div>
                                        @elseif($actualStatus !== \App\Models\Tagihan::STATUS_SUDAH_BAYAR)
                                            <div class="text-xs text-gray-500">
                                                {{ $jatuhTempo->diffForHumans() }}
                                            </div>
                                        @else
                                            <div class="text-xs text-green-600">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Lunas
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <!-- View Detail -->
                                        <a href="{{ route('pelanggan.tagihan.show', $item) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                           title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <!-- Tombol Bayar: hanya untuk yang belum bayar dan tidak pending -->
                                        @if(in_array($actualStatus, ['belum_bayar', 'terlambat']))
                                            <a href="{{ route('pelanggan.tagihan.bayar', $item) }}" 
                                               class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs transition-colors duration-200 inline-flex items-center"
                                               title="Bayar Tagihan">
                                                <i class="fas fa-credit-card mr-1"></i>
                                                Bayar
                                            </a>
                                        @endif
                                        
                                        <!-- Tombol Cancel: untuk pembayaran pending -->
                                        @if($actualStatus == \App\Models\Tagihan::STATUS_MENUNGGU_KONFIRMASI && $pembayaran)
                                            <form method="POST" action="{{ route('pelanggan.bayar.cancel', $item) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs transition-colors duration-200 inline-flex items-center"
                                                        onclick="return confirm('Yakin ingin membatalkan pembayaran ini?')"
                                                        title="Batalkan Pembayaran">
                                                    <i class="fas fa-times mr-1"></i>
                                                    Batal
                                                </button>
                                            </form>
                                        @endif
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
                    <p class="text-gray-500 mb-6">
                        @if(request()->hasAny(['search', 'status', 'tahun']))
                            Tidak ada tagihan yang sesuai dengan filter yang dipilih.
                        @else
                            Anda belum memiliki tagihan. Tagihan akan muncul setelah meteran dibaca oleh petugas.
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'status', 'tahun']))
                        <a href="{{ route('pelanggan.tagihan.index') }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 inline-flex items-center">
                            <i class="fas fa-times mr-2"></i>
                            Hapus Filter
                        </a>
                    @endif
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
                        {{ $tagihan->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Auto-hide success/error messages after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('[role="alert"]');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);

    // Konfirmasi sebelum pembayaran
    document.querySelectorAll('a[href*="bayar"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            if (!confirm('Apakah Anda yakin ingin melanjutkan pembayaran tagihan ini?')) {
                e.preventDefault();
            }
        });
    });

    // Auto refresh untuk status pembayaran yang pending
    function setupAutoRefresh() {
        const pendingPayments = document.querySelectorAll('.status-pending');
        
        if (pendingPayments.length > 0) {
            console.log(`Found ${pendingPayments.length} pending payments, setting up auto refresh...`);
            
            // Refresh setiap 2 menit untuk pembayaran pending
            const refreshInterval = setInterval(function() {
                if (!document.hidden) {
                    refreshPaymentStatus();
                }
            }, 120000); // 2 menit

            // Juga check secara periodik dengan AJAX untuk update yang lebih smooth
            const ajaxCheckInterval = setInterval(function() {
                if (!document.hidden) {
                    updateStatistics();
                }
            }, 30000); // 30 detik
        }
    }

    // Fungsi untuk refresh status pembayaran
    function refreshPaymentStatus() {
        const refreshButton = document.querySelector('button[onclick="refreshPaymentStatus()"]');
        if (refreshButton) {
            refreshButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Refreshing...';
            refreshButton.disabled = true;
        }
        
        // Update statistik dulu, lalu reload jika masih ada pending
        updateStatistics().then(data => {
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        });
    }

    // Fungsi untuk update statistik via AJAX
    function updateStatistics() {
        return fetch('{{ route("pelanggan.tagihan.statistics") }}')
            .then(response => response.json())
            .then(data => {
                console.log('Statistics updated:', data);
                
                // Update statistik di dashboard cards dengan ID yang jelas
                const totalTagihanEl = document.getElementById('stat-total-tagihan');
                const belumBayarEl = document.getElementById('stat-belum-bayar');
                const terlambatEl = document.getElementById('stat-terlambat');
                const sudahBayarEl = document.getElementById('stat-sudah-bayar');
                const totalHutangEl = document.getElementById('stat-total-hutang');

                if (totalTagihanEl) totalTagihanEl.textContent = data.totalTagihan || 0;
                if (belumBayarEl) belumBayarEl.textContent = data.belumBayar || 0;
                if (terlambatEl) terlambatEl.textContent = data.terlambat || 0;
                if (sudahBayarEl) sudahBayarEl.textContent = data.sudahBayar || 0;
                
                if (data.sudahBayar > currentSudahBayar) {
                    setTimeout(() => window.location.reload(), 1000);
                }

                if (totalHutangEl) {
                    const hutang = data.totalHutang || 0;
                    if (hutang > 0) {
                        totalHutangEl.innerHTML = '<span class="text-red-600">Rp ' + new Intl.NumberFormat('id-ID').format(hutang) + '</span>';
                    } else {
                        totalHutangEl.innerHTML = '<span class="text-green-600">Lunas</span>';
                    }
                }

                // Update info card untuk menunggu konfirmasi
                const menungguKonfirmasi = data.menungguKonfirmasi || 0;
                const infoCard = document.querySelector('.pending-notification');
                
                if (menungguKonfirmasi > 0) {
                    if (!infoCard) {
                        // Buat info card jika belum ada
                        const cardHtml = `
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 pending-notification">
                                <div class="flex items-center">
                                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                    <span class="text-blue-800">
                                        Anda memiliki <strong>${menungguKonfirmasi}</strong> tagihan yang sedang menunggu konfirmasi pembayaran.
                                        Status akan diperbarui otomatis setelah konfirmasi dari gateway pembayaran.
                                    </span>
                                    <button onclick="refreshPaymentStatus()" class="ml-auto bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                        <i class="fas fa-sync-alt mr-1"></i> Refresh
                                    </button>
                                </div>
                            </div>
                        `;
                        const statsCards = document.querySelector('.grid');
                        statsCards.insertAdjacentHTML('afterend', cardHtml);
                    } else {
                        // Update jumlah di info card yang sudah ada
                        const countSpan = infoCard.querySelector('strong');
                        if (countSpan) countSpan.textContent = menungguKonfirmasi;
                    }
                } else {
                    // Hapus info card jika tidak ada lagi pembayaran pending
                    if (infoCard) {
                        infoCard.style.transition = 'opacity 0.5s ease-out';
                        infoCard.style.opacity = '0';
                        setTimeout(() => infoCard.remove(), 500);
                    }
                }

                // Jika tidak ada lagi pembayaran pending, reload untuk update tabel
                const stillPending = document.querySelectorAll('.status-pending');
                if (menungguKonfirmasi === 0 && stillPending.length > 0) {
                    console.log('No more pending payments, reloading page...');
                    setTimeout(() => window.location.reload(), 2000);
                }

                return data;
            })
            .catch(error => {
                console.error('Statistics update failed:', error);
                return null;
            });
    }

    // Jalankan setup auto refresh
    setupAutoRefresh();

    // Handle visibility change untuk pause/resume auto refresh
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            // Ketika tab kembali aktif, cek status terbaru
            const pendingPayments = document.querySelectorAll('.status-pending');
            if (pendingPayments.length > 0) {
                // Tunggu sebentar lalu refresh
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        }
    });

    // Handle payment button loading state
    document.querySelectorAll('a[href*="bayar"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            // Add loading state
            this.classList.add('btn-loading');
            this.style.pointerEvents = 'none';
            
            // Remove loading state after 10 seconds as fallback
            setTimeout(() => {
                this.classList.remove('btn-loading');
                this.style.pointerEvents = 'auto';
            }, 10000);
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* Custom styles untuk status badges */
    .status-badge {
        animation: pulse 2s infinite;
    }
    
    .status-pending {
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.8;
        }
    }
    
    /* Loading state untuk tombol */
    .btn-loading {
        opacity: 0.6;
        cursor: not-allowed;
        position: relative;
    }
    
    .btn-loading::after {
        content: '';
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        width: 12px;
        height: 12px;
        border: 2px solid transparent;
        border-top: 2px solid currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
    
    /* Hover effects untuk rows berdasarkan status */
    .bg-red-50:hover {
        background-color: #fee2e2 !important;
    }
    
    .bg-blue-50:hover {
        background-color: #dbeafe !important;
    }
    
    .bg-green-50:hover {
        background-color: #dcfce7 !important;
    }
    
    /* Status specific styling */
    .status-paid {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .status-overdue {
        background-color: #fee2e2;
        color: #991b1b;
    }
    
    .status-unpaid {
        background-color: #fef3c7;
        color: #92400e;
    }

    /* Smooth transitions */
    tr {
        transition: all 0.2s ease-in-out;
    }
    
    .inline-flex {
        transition: all 0.2s ease-in-out;
    }
    
    /* Enhanced button styles */
    .bg-green-600:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(34, 197, 94, 0.3);
    }
    
    .bg-red-600:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
    }
    
    .text-blue-600:hover {
        transform: scale(1.1);
    }
    
    .text-purple-600:hover {
        transform: scale(1.1);
    }
    
    /* Info notification untuk menunggu konfirmasi */
    .pending-notification {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border-left: 4px solid #3b82f6;
        animation: slideIn 0.5s ease-out;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        .grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-5 {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .text-2xl {
            font-size: 1.5rem;
        }
        
        .text-lg {
            font-size: 1rem;
        }
    }
    
    @media (max-width: 640px) {
        .grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-5 {
            grid-template-columns: 1fr;
        }
        
        .space-x-2 > * + * {
            margin-left: 0.25rem;
        }
        
        .px-3.py-1 {
            padding: 0.25rem 0.5rem;
        }
    }
</style>
@endpush
@endsection