@extends('layouts.app')

@section('title', 'Hapus Tagihan')

@section('header', 'Hapus Tagihan')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="max-w-2xl mx-auto">
        <!-- Warning Header -->
        <div class="text-center mb-6">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Konfirmasi Penghapusan</h2>
            <p class="text-gray-600">Apakah Anda yakin ingin menghapus tagihan ini?</p>
        </div>

        <!-- Tagihan Details Card -->
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-red-800 mb-4 flex items-center">
                <i class="fas fa-file-invoice mr-2"></i>
                Detail Tagihan yang Akan Dihapus
            </h3>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">ID Tagihan:</span>
                    <span class="text-gray-900">{{ $tagihan->id }}</span>
                </div>
                
                @if($tagihan->nomor_tagihan)
                <div class="flex justify-between items-start">
                    <span class="font-medium text-gray-700">Nomor Tagihan:</span>
                    <span class="text-gray-900 font-semibold text-right">{{ $tagihan->nomor_tagihan }}</span>
                </div>
                @endif
                
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">Pelanggan:</span>
                    <span class="text-gray-900 font-medium">{{ $tagihan->user->name ?? 'N/A' }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">No. Meteran:</span>
                    <span class="text-gray-900">{{ $tagihan->meteran->nomor_meteran ?? 'N/A' }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">Periode:</span>
                    <span class="text-gray-900 font-medium">{{ $tagihan->bulan }} {{ $tagihan->tahun }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">Pemakaian:</span>
                    <span class="text-gray-900">{{ number_format($tagihan->jumlah_pemakaian, 0) }} m³</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">Total Tagihan:</span>
                    <span class="text-green-600 font-bold text-lg">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">Status:</span>
                    <span class="text-gray-900">
                        @switch($tagihan->status)
                            @case('belum_bayar')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-clock mr-1"></i> Belum Bayar
                                </span>
                                @break
                            @case('sudah_bayar')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-check-circle mr-1"></i> Sudah Bayar
                                </span>
                                @break
                            @case('terlambat')
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Terlambat
                                </span>
                                @break
                            @default
                                {{ ucfirst($tagihan->status) }}
                        @endswitch
                    </span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">Jatuh Tempo:</span>
                    <span class="text-gray-900">{{ $tagihan->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->format('d M Y') : '-' }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">Dibuat:</span>
                    <span class="text-gray-900">{{ $tagihan->created_at ? $tagihan->created_at->format('d M Y, H:i') : '-' }}</span>
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
                            <li>Data tagihan yang dihapus tidak dapat dikembalikan</li>
                            <li>Riwayat pembacaan meter untuk periode ini akan hilang</li>
                            <li>Jika ada pembayaran terkait, pastikan untuk menanganinya terlebih dahulu</li>
                            @if($tagihan->status == 'sudah_bayar')
                            <li class="text-red-600 font-medium">PERHATIAN: Tagihan ini sudah dibayar, penghapusan tidak disarankan</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-center space-x-4">
            <!-- Cancel Button -->
            <a href="{{ route('superuser.tagihan.show', $tagihan->id) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-6 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-times mr-2"></i>
                Batal
            </a>
            
            <!-- Back to Index Button -->
            <a href="{{ route('superuser.tagihan.index') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 px-6 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Daftar
            </a>
            
            <!-- Delete Confirmation Button -->
            <form action="{{ route('superuser.tagihan.destroy', $tagihan->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-lg transition duration-300 flex items-center"
                        onclick="return confirm('Anda yakin ingin menghapus tagihan ini? Tindakan ini tidak dapat dibatalkan!')">
                    <i class="fas fa-trash mr-2"></i>
                    Ya, Hapus Tagihan
                </button>
            </form>
        </div>

        <!-- Additional Info -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">
                Jika Anda tidak yakin, silakan konsultasikan terlebih dahulu atau kembali ke halaman detail tagihan.
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