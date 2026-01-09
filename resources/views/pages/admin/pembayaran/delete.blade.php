@extends('layouts.app')

@section('title', 'Hapus Pembayaran')

@section('header', 'Hapus Pembayaran')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="max-w-2xl mx-auto">
        <!-- Warning Header -->
        <div class="text-center mb-6">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Konfirmasi Penghapusan</h2>
            <p class="text-gray-600">Apakah Anda yakin ingin menghapus pembayaran ini?</p>
        </div>

        <!-- Pembayaran Details Card -->
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-red-800 mb-4 flex items-center">
                <i class="fas fa-file-invoice mr-2"></i>
                Detail Pembayaran yang Akan Dihapus
            </h3>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">ID Pembayaran:</span>
                    <span class="text-gray-900">{{ $pembayaran->id }}</span>
                </div>
                
                @if($pembayaran->nomor_pembayaran)
                <div class="flex justify-between items-start">
                    <span class="font-medium text-gray-700">Nomor Pembayaran:</span>
                    <span class="text-gray-900 font-semibold text-right">{{ $pembayaran->nomor_pembayaran }}</span>
                </div>
                @endif
                
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">Pelanggan:</span>
                    <span class="text-gray-900 font-medium">
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
                    </span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">No. Meteran:</span>
                    <span class="text-gray-900">
                        @if($pembayaran->tagihan)
                            @if($pembayaran->tagihan->meteran)
                                {{ $pembayaran->tagihan->meteran->nomor_meteran ?? 'N/A' }}
                            @elseif($pembayaran->tagihan->pembacaanMeteran && $pembayaran->tagihan->pembacaanMeteran->meteran)
                                {{ $pembayaran->tagihan->pembacaanMeteran->meteran->nomor_meteran ?? 'N/A' }}
                            @else
                                <span class="text-gray-400">Meteran tidak ditemukan</span>
                            @endif
                        @else
                            <span class="text-gray-400">Data tidak tersedia</span>
                        @endif
                    </span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">Jumlah:</span>
                    <span class="text-green-600 font-bold text-lg">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">Tanggal Pembayaran:</span>
                    <span class="text-gray-900">{{ $pembayaran->tanggal_pembayaran ? \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d M Y') : '-' }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">Metode:</span>
                    <span class="text-gray-900">
                        @if($pembayaran->metode_pembayaran)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($pembayaran->metode_pembayaran == 'tunai') bg-green-100 text-green-800
                                @elseif($pembayaran->metode_pembayaran == 'transfer') bg-blue-100 text-blue-800
                                @else bg-purple-100 text-purple-800 @endif">
                                {{ ucfirst(str_replace('-', ' ', $pembayaran->metode_pembayaran)) }}
                            </span>
                        @else
                            -
                        @endif
                    </span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">Status:</span>
                    <span class="text-gray-900">
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
                    </span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">Verifikasi:</span>
                    <span class="text-gray-900">
                        @if($pembayaran->is_verified)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-shield-check mr-1"></i>
                                Terverifikasi
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Belum Verifikasi
                            </span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Warning Message -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Peringatan Penting</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Data pembayaran yang dihapus tidak dapat dikembalikan</li>
                            <li>Status tagihan terkait akan dikembalikan menjadi belum bayar</li>
                            <li>Jika pembayaran ini terkait dengan transaksi online, pastikan untuk menangani pembatalan di penyedia pembayaran</li>
                            @if($pembayaran->is_verified)
                            <li class="text-red-600 font-medium">PERHATIAN: Pembayaran ini sudah diverifikasi, penghapusan tidak disarankan</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-center space-x-4">
            <!-- Cancel Button -->
            <a href="{{ route('admin.pembayaran.show', $pembayaran->id) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-6 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-times mr-2"></i>
                Batal
            </a>
            
            <!-- Back to Index Button -->
            <a href="{{ route('admin.pembayaran.index') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 px-6 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Daftar
            </a>
            
            <!-- Delete Confirmation Button -->
            <form action="{{ route('admin.pembayaran.destroy', $pembayaran->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-lg transition duration-300 flex items-center">
                    <i class="fas fa-trash mr-2"></i>
                    Ya, Hapus Pembayaran
                </button>
            </form>
        </div>

        <!-- Additional Info -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">
                Jika Anda tidak yakin, silakan konsultasikan terlebih dahulu atau kembali ke halaman detail pembayaran.
            </p>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Optional: Auto focus on cancel button for accessibility
    document.addEventListener('DOMContentLoaded', function() {
        const cancelButton = document.querySelector('a[href*="show"]');
        if (cancelButton) {
            cancelButton.focus();
        }
    });
</script>
@endsection
@endsection