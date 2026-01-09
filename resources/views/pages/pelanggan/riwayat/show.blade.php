@extends('layouts.app')

@section('title', 'Detail Pembayaran')
@section('header', 'Detail Pembayaran')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('pelanggan.riwayat.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <!-- Payment Details Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 sm:px-6 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900">Informasi Pembayaran</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column - Payment Details -->
                <div class="space-y-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">ID Pembayaran</h4>
                        <p class="mt-1 text-sm text-gray-900">{{ $pembayaran->id }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Periode Tagihan</h4>
                        <p class="mt-1 text-sm text-gray-900">{{ $pembayaran->tagihan->bulan }} {{ $pembayaran->tagihan->tahun }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Jumlah Pembayaran</h4>
                        <p class="mt-1 text-sm text-gray-900 font-semibold">Rp {{ number_format($pembayaran->jumlah ?? $pembayaran->jumlah_bayar ?? 0, 0, ',', '.') }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Metode Pembayaran</h4>
                        <p class="mt-1 text-sm text-gray-900 capitalize">{{ str_replace(['_', '-'], ' ', $pembayaran->metode_pembayaran) }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Tanggal Pembayaran</h4>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($pembayaran->tanggal_pembayaran)
                                {{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d F Y H:i') }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Status</h4>
                        <div class="mt-1">
                            @if($pembayaran->status == 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>Pending
                                </span>
                            @elseif($pembayaran->status == 'lunas')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Lunas
                                </span>
                            @elseif($pembayaran->status == 'failed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>Gagal
                                </span>
                            @elseif($pembayaran->status == 'cancelled')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-ban mr-1"></i>Dibatalkan
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-question-circle mr-1"></i>
                                    {{ ucfirst($pembayaran->status ?? 'Tidak diketahui') }}
                                </span>
                            @endif

                            {{-- Verification status indicator --}}
                            @if($pembayaran->status == 'lunas')
                                @if($pembayaran->is_verified)
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-shield-check mr-1"></i>
                                            Terverifikasi
                                        </span>
                                    </div>
                                @else
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Belum Verifikasi
                                        </span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    
                    @if($pembayaran->keterangan)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Keterangan</h4>
                        <p class="mt-1 text-sm text-gray-900">{{ $pembayaran->keterangan }}</p>
                    </div>
                    @endif

                    @if($pembayaran->catatan_admin)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Catatan Admin</h4>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded">{{ $pembayaran->catatan_admin }}</p>
                    </div>
                    @endif
                </div>
                
                <!-- Right Column - Meter and Bill Details -->
                <div class="space-y-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Nomor Meteran</h4>
                        <p class="mt-1 text-sm text-gray-900">{{ $pembayaran->tagihan->meteran->nomor_meteran }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Alamat</h4>
                        <p class="mt-1 text-sm text-gray-900">{{ $pembayaran->tagihan->meteran->alamat }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Jumlah Pemakaian</h4>
                        <p class="mt-1 text-sm text-gray-900">
                            @if(isset($pembayaran->tagihan->jumlah_pemakaian))
                                {{ $pembayaran->tagihan->jumlah_pemakaian }} m³
                            @elseif(isset($pembayaran->tagihan->pemakaian))
                                {{ $pembayaran->tagihan->pemakaian }} m³
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Biaya Tagihan</h4>
                        <p class="mt-1 text-sm text-gray-900">
                            @if(isset($pembayaran->tagihan->jumlah_tagihan))
                                Rp {{ number_format($pembayaran->tagihan->jumlah_tagihan, 0, ',', '.') }}
                            @elseif(isset($pembayaran->tagihan->total_tagihan))
                                Rp {{ number_format($pembayaran->tagihan->total_tagihan, 0, ',', '.') }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Biaya Admin</h4>
                        <p class="mt-1 text-sm text-gray-900">
                            @if(isset($pembayaran->tagihan->biaya_admin))
                                Rp {{ number_format($pembayaran->tagihan->biaya_admin, 0, ',', '.') }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Denda Keterlambatan</h4>
                        <p class="mt-1 text-sm text-gray-900">
                            @if(isset($pembayaran->tagihan->denda))
                                Rp {{ number_format($pembayaran->tagihan->denda, 0, ',', '.') }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Tanggal Jatuh Tempo</h4>
                        <p class="mt-1 text-sm text-gray-900">
                            @if(isset($pembayaran->tagihan->tanggal_jatuh_tempo))
                                {{ \Carbon\Carbon::parse($pembayaran->tagihan->tanggal_jatuh_tempo)->format('d F Y') }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </p>
                    </div>

                    @if($pembayaran->processed_by)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Diproses Oleh</h4>
                        <p class="mt-1 text-sm text-gray-900">{{ $pembayaran->processed_by }}</p>
                    </div>
                    @endif

                    @if($pembayaran->processed_at)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Tanggal Diproses</h4>
                        <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($pembayaran->processed_at)->format('d F Y H:i') }}</p>
                    </div>
                    @endif

                    @if($pembayaran->verified_at && $pembayaran->is_verified)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Tanggal Verifikasi</h4>
                        <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($pembayaran->verified_at)->format('d F Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Proof of Payment -->
            @if($pembayaran->bukti_pembayaran)
            <div class="mt-8 border-t border-gray-200 pt-6">
                <h4 class="text-sm font-medium text-gray-500 mb-4">Bukti Pembayaran</h4>
                <div class="flex justify-center">
                    <img src="{{ Storage::url($pembayaran->bukti_pembayaran) }}" alt="Bukti Pembayaran" class="max-w-full h-auto rounded-lg shadow-md" style="max-height: 400px;">
                </div>
            </div>
            @endif
            
            <!-- Action Buttons -->
            <div class="mt-8 border-t border-gray-200 pt-6 flex flex-wrap gap-3">
                @if($pembayaran->status == 'lunas' && $pembayaran->is_verified)
                    <a href="{{ route('pelanggan.riwayat.cetak', $pembayaran->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-download mr-2"></i>Download Kwitansi
                    </a>
                @endif
                
                @if(in_array($pembayaran->status, ['failed', 'cancelled']))
                    <a href="{{ route('pelanggan.riwayat.resubmit', $pembayaran->id) }}" class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 active:bg-orange-900 focus:outline-none focus:border-orange-900 focus:ring ring-orange-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-redo mr-2"></i>Kirim Ulang Pembayaran
                    </a>
                @endif
                
                @if($pembayaran->status == 'pending')
                    <button onclick="confirmCancel()" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-trash mr-2"></i>Batalkan Pembayaran
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Cancel Confirmation Modal -->
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-2">Batalkan Pembayaran?</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin membatalkan pembayaran ini? Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div class="flex justify-center gap-4 mt-4">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Batal
                </button>
                <form action="{{ route('pelanggan.riwayat.cancel', $pembayaran->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Ya, Batalkan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmCancel() {
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endpush
@endsection