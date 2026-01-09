@extends('layouts.app')

@section('title', 'Pembayaran Tagihan')

@section('header', 'Pembayaran Tagihan')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div class="flex items-center">
        <a href="{{ route('pelanggan.tagihan.index') }}" 
           class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar Tagihan
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Pembayaran -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Pembayaran Online</h2>
                    <p class="text-sm text-gray-600 mt-1">Bayar tagihan Anda dengan berbagai metode pembayaran yang tersedia</p>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Alert untuk error -->
                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4" role="alert">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Error</h3>
                                    <p class="mt-1 text-sm text-red-700">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Alert untuk success -->
                    @if(session('success'))
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4" role="alert">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-green-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-green-800">Berhasil</h3>
                                    <p class="mt-1 text-sm text-green-700">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Alert untuk info -->
                    @if(session('info'))
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4" role="alert">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Informasi</h3>
                                    <p class="mt-1 text-sm text-blue-700">{{ session('info') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Payment Gateway Information -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-credit-card text-blue-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-blue-800">Metode Pembayaran Tersedia</h4>
                                <div class="mt-2 text-sm text-blue-700">
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-3">
                                        <!-- Bank Transfer -->
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-university text-blue-600"></i>
                                            <span class="text-xs">Bank Transfer</span>
                                        </div>
                                        <!-- Credit Card -->
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-credit-card text-blue-600"></i>
                                            <span class="text-xs">Kartu Kredit</span>
                                        </div>
                                        <!-- E-Wallet -->
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-wallet text-blue-600"></i>
                                            <span class="text-xs">E-Wallet</span>
                                        </div>
                                        <!-- Virtual Account -->
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-mobile-alt text-blue-600"></i>
                                            <span class="text-xs">Virtual Account</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-800 mb-3">Ringkasan Pembayaran</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Periode Tagihan</span>
                                <span class="font-medium">{{ $tagihan->bulan }} {{ $tagihan->tahun }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">No. Meteran</span>
                                <span class="font-medium">{{ $tagihan->meteran->nomor_meteran ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Pemakaian</span>
                                <span class="font-medium">{{ number_format($tagihan->pemakaian, 0) }} m³</span>
                            </div>
                            <div class="border-t pt-2">
                                <div class="flex justify-between">
                                    <span class="font-semibold text-gray-800">Total Pembayaran</span>
                                    <span class="font-bold text-green-600 text-lg">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Button -->
                    <div class="flex items-center justify-center pt-6 border-t border-gray-200">
                        <button id="pay-button" 
                                class="w-full md:w-auto px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-credit-card mr-2"></i>
                            <span id="button-text">Bayar Sekarang - Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</span>
                        </button>
                    </div>

                    <!-- Loading State -->
                    <div id="loading-state" class="hidden">
                        <div class="flex items-center justify-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            <span class="ml-3 text-gray-600">Memproses pembayaran...</span>
                        </div>
                    </div>

                    <!-- Security Notice -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-shield-alt text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-green-800">Keamanan Terjamin</h4>
                                <div class="mt-2 text-sm text-green-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Pembayaran dilindungi dengan enkripsi SSL 256-bit</li>
                                        <li>Tidak menyimpan data kartu kredit Anda</li>
                                        <li>Transaksi diproses oleh Midtrans (PCI DSS Certified)</li>
                                        <li>Konfirmasi pembayaran real-time</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Tagihan -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md sticky top-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Detail Tagihan</h3>
                </div>
                
                <div class="p-6 space-y-4">
                    <!-- Periode -->
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Periode</span>
                        <span class="font-medium text-gray-900">{{ $tagihan->bulan }} {{ $tagihan->tahun }}</span>
                    </div>
                    
                    <!-- No. Meteran -->
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">No. Meteran</span>
                        <span class="font-medium text-gray-900">{{ $tagihan->meteran->nomor_meteran ?? 'N/A' }}</span>
                    </div>
                    
                    <!-- Alamat -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-600 mb-1">Alamat</span>
                        <span class="text-sm text-gray-900">{{ $tagihan->meteran->alamat ?? 'N/A' }}</span>
                    </div>
                    
                    <div class="border-t pt-4">
                        <!-- Meter Awal -->
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Meter Awal</span>
                            <span class="text-sm text-gray-900">{{ number_format($tagihan->meter_awal, 0) }} m³</span>
                        </div>
                        
                        <!-- Meter Akhir -->
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Meter Akhir</span>
                            <span class="text-sm text-gray-900">{{ number_format($tagihan->meter_akhir, 0) }} m³</span>
                        </div>
                        
                        <!-- Pemakaian -->
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Pemakaian</span>
                            <span class="font-medium text-blue-600">{{ number_format($tagihan->pemakaian, 0) }} m³</span>
                        </div>
                        
                        <!-- Tarif per m³ -->
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Tarif per m³</span>
                            <span class="text-sm text-gray-900">Rp {{ number_format($tagihan->tarif_per_m3, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <div class="border-t pt-4">
                        <!-- Total Tagihan -->
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-900">Total Tagihan</span>
                            <span class="text-xl font-bold text-green-600">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <div class="border-t pt-4">
                        <!-- Status -->
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Status</span>
                            @if($tagihan->status == \App\Models\Tagihan::STATUS_BELUM_BAYAR)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    Belum Bayar
                                </span>
                            @elseif($tagihan->status == \App\Models\Tagihan::STATUS_TERLAMBAT)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Terlambat
                                </span>
                            @elseif($tagihan->status == \App\Models\Tagihan::STATUS_MENUNGGU_KONFIRMASI)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-hourglass-half mr-1"></i>
                                    Menunggu Konfirmasi
                                </span>
                            @elseif($tagihan->status == \App\Models\Tagihan::STATUS_SUDAH_BAYAR)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Sudah Bayar
                                </span>
                            @endif
                        </div>
                        
                        <!-- Jatuh Tempo -->
                        @if($tagihan->tanggal_jatuh_tempo)
                            @php
                                $jatuhTempo = \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo);
                                $isOverdue = $jatuhTempo->isPast();
                            @endphp
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Jatuh Tempo</span>
                                <div class="text-right">
                                    <div class="text-sm {{ $isOverdue ? 'text-red-600 font-medium' : 'text-gray-900' }}">
                                        {{ $jatuhTempo->format('d/m/Y') }}
                                    </div>
                                    @if($isOverdue)
                                        <div class="text-xs text-red-500">
                                            Terlambat {{ $jatuhTempo->diffForHumans() }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Order ID Info -->
                    <div class="bg-gray-50 rounded-lg p-3 mt-4">
                        <div class="text-xs text-gray-600">
                            <div class="flex justify-between">
                                <span>Order ID:</span>
                                <span class="font-mono">{{ $orderId }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Midtrans Snap.js -->
<script src="{{ $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" 
        data-client-key="{{ $clientKey }}"></script>

<script>
    // Configuration
    const snapToken = '{{ $snapToken }}';
    const orderId = '{{ $orderId }}';
    const payButton = document.getElementById('pay-button');
    const loadingState = document.getElementById('loading-state');
    const buttonText = document.getElementById('button-text');
    
    // URLs untuk callback
    const frontendCallbackUrl = '{{ route("pelanggan.bayar.callback") }}';
    const tagihanIndexUrl = '{{ route("pelanggan.tagihan.index") }}';
    
    // Prevent multiple clicks
    let isProcessing = false;
    
    // Debug: Log konfigurasi
    console.log('Payment Configuration:', {
        snapToken: snapToken ? 'Token exists' : 'No token',
        orderId: orderId,
        callbackUrl: frontendCallbackUrl,
        snapLoaded: typeof snap !== 'undefined'
    });
    
    // Check if Snap is loaded
    function checkSnapLoaded() {
        if (typeof snap === 'undefined') {
            showNotification('Payment gateway tidak dimuat. Silakan refresh halaman.', 'error');
            return false;
        }
        return true;
    }
    
    // Payment button click handler
    payButton.addEventListener('click', function() {
        console.log('Payment button clicked');
        
        // Prevent multiple clicks
        if (isProcessing || this.disabled) {
            console.log('Payment already processing or button disabled');
            return false;
        }
        
        // Check if Snap is loaded
        if (!checkSnapLoaded()) {
            return false;
        }
        
        // Validate snap token
        if (!snapToken || snapToken === '') {
            showNotification('Token pembayaran tidak valid. Silakan refresh halaman dan coba lagi.', 'error');
            console.error('Invalid snap token:', snapToken);
            return false;
        }
        
        console.log('Starting payment process...');
        
        // Set processing state
        isProcessing = true;
        this.disabled = true;
        buttonText.textContent = 'Memproses...';
        loadingState.classList.remove('hidden');
        
        // Trigger Midtrans Snap
        try {
            snap.pay(snapToken, {
                onSuccess: function(result) {
                    console.log('Payment success:', result);
                    
                    // Show success message
                    showNotification('Pembayaran berhasil! Mengalihkan halaman...', 'success');
                    
                    // Redirect dengan parameter yang benar
                    setTimeout(function() {
                        const params = new URLSearchParams({
                            order_id: orderId,
                            transaction_status: 'settlement',
                            result_type: 'success'
                        });
                        window.location.href = frontendCallbackUrl + '?' + params.toString();
                    }, 2000);
                },
                onPending: function(result) {
                    console.log('Payment pending:', result);
                    
                    // Show pending message
                    showNotification('Pembayaran sedang diproses. Mengalihkan halaman...', 'info');
                    
                    // Redirect dengan parameter yang benar
                    setTimeout(function() {
                        const params = new URLSearchParams({
                            order_id: orderId,
                            transaction_status: 'pending',
                            result_type: 'pending'
                        });
                        window.location.href = frontendCallbackUrl + '?' + params.toString();
                    }, 2000);
                },
                onError: function(result) {
                    console.log('Payment error:', result);
                    resetButton();
                    showNotification('Pembayaran gagal! Error: ' + (result.status_message || 'Unknown error'), 'error');
                },
                onClose: function() {
                    console.log('Payment popup closed');
                    resetButton();
                    showNotification('Jendela pembayaran ditutup.', 'info');
                }
            });
        } catch (error) {
            console.error('Error triggering Snap payment:', error);
            resetButton();
            showNotification('Terjadi kesalahan saat memuat pembayaran: ' + error.message, 'error');
        }
    });
    
    // Reset button state
    function resetButton() {
        console.log('Resetting button state');
        isProcessing = false;
        payButton.disabled = false;
        buttonText.textContent = 'Bayar Sekarang - Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}';
        loadingState.classList.add('hidden');
    }
    
    // Show notification function
    function showNotification(message, type) {
        console.log('Showing notification:', type, message);
        
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 max-w-md p-4 rounded-lg shadow-lg z-50 notification-enter ${getNotificationClass(type)}`;
        notification.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas ${getNotificationIcon(type)}"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 8 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }
        }, 8000);
    }
    
    // Get notification class based on type
    function getNotificationClass(type) {
        switch(type) {
            case 'success': return 'bg-green-50 border border-green-200 text-green-800';
            case 'error': return 'bg-red-50 border border-red-200 text-red-800';
            case 'info': return 'bg-blue-50 border border-blue-200 text-blue-800';
            case 'warning': return 'bg-yellow-50 border border-yellow-200 text-yellow-800';
            default: return 'bg-gray-50 border border-gray-200 text-gray-800';
        }
    }
    
    // Get notification icon based on type
    function getNotificationIcon(type) {
        switch(type) {
            case 'success': return 'fa-check-circle text-green-400';
            case 'error': return 'fa-exclamation-circle text-red-400';
            case 'info': return 'fa-info-circle text-blue-400';
            case 'warning': return 'fa-exclamation-triangle text-yellow-400';
            default: return 'fa-bell text-gray-400';
        }
    }
    
    // Auto-hide session alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('[role="alert"]');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => {
                if (alert.parentElement) {
                    alert.remove();
                }
            }, 500);
        });
    }, 8000);
    
    // Add global error handler
    window.addEventListener('error', function(e) {
        console.error('Global error:', e.error);
        if (e.message && e.message.toLowerCase().includes('snap')) {
            resetButton();
            showNotification('Terjadi kesalahan saat memuat payment gateway. Silakan refresh halaman.', 'error');
        }
    });
    
    // Check if everything is loaded correctly
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, checking payment setup...');
        
        setTimeout(function() {
            if (typeof snap === 'undefined') {
                showNotification('Payment gateway tidak dimuat dengan benar. Silakan refresh halaman.', 'error');
                console.error('Snap.js not loaded properly');
            } else {
                console.log('Payment setup completed successfully');
            }
        }, 2000);
    });
</script>
@endpush

@push('styles')
<style>
    /* Loading animation */
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    /* Sticky behavior for detail card */
    @media (min-width: 1024px) {
        .sticky {
            position: sticky;
            top: 1.5rem;
        }
    }
    
    /* Payment button hover effect */
    #pay-button {
        transition: all 0.3s ease;
    }
    
    #pay-button:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
    }
    
    #pay-button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    /* Smooth transitions */
    .transition-all {
        transition: all 0.3s ease;
    }
    
    /* Payment methods grid responsive */
    @media (max-width: 768px) {
        .grid-cols-4 {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    /* Notification styles */
    .notification-enter {
        animation: slideInRight 0.3s ease-out;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    /* Status badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    /* Alert fade out animation */
    [role="alert"] {
        transition: opacity 0.5s ease-out;
    }
    
    /* Responsive improvements */
    @media (max-width: 640px) {
        .px-6 {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        .py-4 {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }
        
        .text-xl {
            font-size: 1.125rem;
        }
        
        .text-lg {
            font-size: 1rem;
        }
    }
    
    /* Print styles */
    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush
@endsection