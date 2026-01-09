@extends('layouts.app')

@section('title', 'Detail Tagihan')

@section('header', 'Detail Tagihan')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('pelanggan.tagihan.index') }}" 
           class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar Tagihan
        </a>
        
        <!-- Action Buttons -->
        <div class="flex items-center space-x-3">
            @if(in_array($tagihan->status, [\App\Models\Tagihan::STATUS_BELUM_BAYAR, \App\Models\Tagihan::STATUS_TERLAMBAT]))
                <a href="{{ route('pelanggan.tagihan.bayar', $tagihan) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 inline-flex items-center">
                    <i class="fas fa-credit-card mr-2"></i>
                    Bayar Sekarang
                </a>
            @endif
            
            @if($tagihan->status == \App\Models\Tagihan::STATUS_SUDAH_BAYAR)
                <a href="{{ route('pelanggan.tagihan.download-pdf', $tagihan) }}" 
                   class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 inline-flex items-center"
                   title="Download PDF">
                    <i class="fas fa-download mr-2"></i>
                    Download PDF
                </a>
            @endif
            
            @if($tagihan->status == \App\Models\Tagihan::STATUS_MENUNGGU_KONFIRMASI)
                <button onclick="cancelPayment({{ $tagihan->id }})" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 inline-flex items-center">
                    <i class="fas fa-times mr-2"></i>
                    Batal Pembayaran
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @php
        // Validasi dan hitung ulang data jika perlu
        $pemakaian = $tagihan->pemakaian ?? 0;
        $meterAwal = $tagihan->meter_awal ?? 0;
        $meterAkhir = $tagihan->meter_akhir ?? 0;
        $totalTagihan = $tagihan->total_tagihan ?? 0;
        $tarifPerM3 = $tagihan->tarif_per_m3 ?? 0;
        
        // Hitung ulang jika data kosong
        if ($pemakaian == 0 && $meterAwal > 0 && $meterAkhir > 0) {
            $pemakaian = $meterAkhir - $meterAwal;
        }
        
        if ($totalTagihan == 0 && $pemakaian > 0 && $tarifPerM3 > 0) {
            $totalTagihan = $pemakaian * $tarifPerM3;
        }
    @endphp
    
    <div class="text-center">
        <div class="bg-blue-100 rounded-lg p-4">
            <i class="fas fa-tachometer-alt text-blue-600 text-2xl mb-2"></i>
            <h4 class="text-sm font-medium text-gray-700">Meter Awal</h4>
            <p class="text-xl font-bold text-blue-600">{{ number_format($meterAwal, 0) }} m³</p>
        </div>
    </div>
    
    <div class="text-center">
        <div class="bg-green-100 rounded-lg p-4">
            <i class="fas fa-tachometer-alt text-green-600 text-2xl mb-2"></i>
            <h4 class="text-sm font-medium text-gray-700">Meter Akhir</h4>
            <p class="text-xl font-bold text-green-600">{{ number_format($meterAkhir, 0) }} m³</p>
        </div>
    </div>
    
    <div class="text-center">
        <div class="bg-purple-100 rounded-lg p-4">
            <i class="fas fa-water text-purple-600 text-2xl mb-2"></i>
            <h4 class="text-sm font-medium text-gray-700">Total Pemakaian</h4>
            <p class="text-xl font-bold text-purple-600">{{ number_format($pemakaian, 0) }} m³</p>
        </div>
    </div>
    </div>

{{-- PERBAIKAN UNTUK bayar.blade.php --}}

{{-- Tambahkan di bagian detail tagihan sidebar --}}
<div class="p-6 space-y-4">
    @php
        // Validasi data untuk pembayaran
        $pemakaian = $tagihan->pemakaian ?? 0;
        $totalTagihan = $tagihan->total_tagihan ?? 0;
        $meterAwal = $tagihan->meter_awal ?? 0;
        $meterAkhir = $tagihan->meter_akhir ?? 0;
        $tarifPerM3 = $tagihan->tarif_per_m3 ?? 0;
        
        // Hitung ulang jika data tidak valid
        if ($pemakaian == 0 && $meterAwal > 0 && $meterAkhir > 0) {
            $pemakaian = $meterAkhir - $meterAwal;
        }
        
        if ($totalTagihan == 0 && $pemakaian > 0 && $tarifPerM3 > 0) {
            $totalTagihan = $pemakaian * $tarifPerM3;
        }
        
        // Jika masih 0, mungkin ada masalah data
        if ($totalTagihan == 0) {
            \Log::error('Invalid tagihan data for payment:', [
                'tagihan_id' => $tagihan->id,
                'pemakaian' => $tagihan->pemakaian,
                'total_tagihan' => $tagihan->total_tagihan,
                'meter_awal' => $tagihan->meter_awal,
                'meter_akhir' => $tagihan->meter_akhir,
                'tarif_per_m3' => $tagihan->tarif_per_m3
            ]);
        }
    @endphp
    
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
            <span class="text-sm text-gray-900">{{ number_format($meterAwal, 0) }} m³</span>
        </div>
        
        <!-- Meter Akhir -->
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm text-gray-600">Meter Akhir</span>
            <span class="text-sm text-gray-900">{{ number_format($meterAkhir, 0) }} m³</span>
        </div>
        
        <!-- Pemakaian -->
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm text-gray-600">Pemakaian</span>
            <span class="font-medium text-blue-600">{{ number_format($pemakaian, 0) }} m³</span>
        </div>
        
        <!-- Tarif per m³ -->
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm text-gray-600">Tarif per m³</span>
            <span class="text-sm text-gray-900">Rp {{ number_format($tarifPerM3, 0, ',', '.') }}</span>
        </div>
    </div>
    
    <div class="border-t pt-4">
        <!-- Total Tagihan -->
        <div class="flex justify-between items-center">
            <span class="text-lg font-semibold text-gray-900">Total Tagihan</span>
            <span class="text-xl font-bold text-green-600">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</span>
        </div>
    </div>
    
    {{-- Validasi sebelum pembayaran --}}
    @if($totalTagihan <= 0)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-red-800">Data Tagihan Tidak Valid</h4>
                    <p class="mt-1 text-sm text-red-700">
                        Total tagihan tidak valid. Silakan hubungi customer service untuk pengecekan data.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
</div>

<!-- Modal for Image Preview -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <button onclick="closeImageModal()" 
                class="absolute top-4 right-4 text-white hover:text-gray-300 text-2xl z-10">
            <i class="fas fa-times"></i>
        </button>
        <img id="modalImage" src="" alt="Preview" class="max-w-full max-h-full object-contain rounded-lg">
    </div>
</div>

@push('scripts')
<script>
    // Image modal functions
    function showImageModal(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.getElementById('imageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageModal();
        }
    });

    // Cancel payment function
    function cancelPayment(tagihanId) {
        if (confirm('Apakah Anda yakin ingin membatalkan pembayaran ini? Tindakan ini tidak dapat dibatalkan.')) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('pelanggan.tagihan.index') }}/${tagihanId}/cancel-pembayaran`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Auto-hide success/error messages
    setTimeout(function() {
        const alerts = document.querySelectorAll('[role="alert"]');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);

    // Print styles
    window.addEventListener('beforeprint', function() {
        document.querySelectorAll('.no-print').forEach(el => {
            el.style.display = 'none';
        });
    });

    window.addEventListener('afterprint', function() {
        document.querySelectorAll('.no-print').forEach(el => {
            el.style.display = '';
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* Print styles */
    @media print {
        .no-print {
            display: none !important;
        }
        
        .print-break {
            page-break-after: always;
        }
        
        body {
            font-size: 12pt;
        }
        
        .shadow-md {
            box-shadow: none !important;
            border: 1px solid #e5e7eb;
        }
    }
    
    /* Modal styles */
    #imageModal {
        backdrop-filter: blur(4px);
    }
    
    /* Hover effects */
    .hover-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    /* Status indicator animations */
    .status-indicator {
        position: relative;
        overflow: hidden;
    }
    
    .status-indicator::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
        to {
            left: 100%;
        }
    }
    
    /* Sticky positioning */
    @media (min-width: 1024px) {
        .sticky {
            position: sticky;
            top: 1.5rem;
        }
    }
</style>
@endpush
@endsection