@extends('layouts.app')

@section('title', 'Detail Pembayaran')

@section('header', 'Detail Pembayaran')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Pembayaran #{{ $pembayaran->id }}</h2>
        <div class="flex space-x-3">
            {{-- Tombol Konfirmasi Pembayaran --}}
            @if(!$pembayaran->is_verified && ($pembayaran->status == 'lunas' || $pembayaran->status == 'pending'))
                <a href="{{ route('admin.pembayaran.confirm', $pembayaran->id) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
                    <i class="fas fa-check-circle mr-2"></i> Konfirmasi Pembayaran
                </a>
            @endif
            
            {{-- Tombol Edit --}}
            @if(!($pembayaran->status == 'lunas' && $pembayaran->is_verified))
                <a href="{{ route('admin.pembayaran.edit', $pembayaran->id) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
            @endif
            
            {{-- Tombol Kembali --}}
            <a href="{{ route('admin.pembayaran.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Pesan Sukses/Error -->
    @if(session('success'))
    <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <p>{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <p>{{ session('error') }}</p>
        </div>
    </div>
    @endif

    @if(session('info'))
    <div class="mb-6 bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded" role="alert">
        <div class="flex items-center">
            <i class="fas fa-info-circle mr-2"></i>
            <p>{{ session('info') }}</p>
        </div>
    </div>
    @endif

    {{-- Peringatan Inkonsistensi Status --}}
    @if($pembayaran->is_verified && $pembayaran->status == 'lunas' && $pembayaran->tagihan && $pembayaran->tagihan->status != 'sudah_bayar')
    <div class="mb-6 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded" role="alert">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <div>
                <p><strong>Peringatan:</strong> Pembayaran sudah terverifikasi tetapi status tagihan belum terupdate.</p>
                <form action="{{ route('admin.pembayaran.confirm', $pembayaran->id) }}" method="POST" class="mt-2 inline">
                    @csrf
                    <input type="hidden" name="admin_note" value="Sinkronisasi status tagihan manual">
                    <button type="submit" 
                            class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded text-sm"
                            onclick="return confirm('Sinkronisasi status tagihan?')">
                        <i class="fas fa-sync mr-1"></i> Sinkronkan Status
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Informasi Pembayaran -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <i class="fas fa-credit-card mr-2 text-blue-600"></i>
                Informasi Pembayaran
            </h3>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">ID Pembayaran:</span>
                    <span class="text-sm text-gray-900">#{{ $pembayaran->id }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Jumlah Bayar:</span>
                    <span class="text-sm font-semibold text-gray-900">
                        Rp {{ number_format($pembayaran->jumlah ?? $pembayaran->jumlah_bayar ?? 0, 0, ',', '.') }}
                    </span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Tanggal Pembayaran:</span>
                    <span class="text-sm text-gray-900">
                        @if($pembayaran->tanggal_pembayaran)
                            {{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d F Y, H:i') }}
                        @else
                            <span class="text-gray-400">Belum diatur</span>
                        @endif
                    </span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Metode Pembayaran:</span>
                    <span class="text-sm text-gray-900">
                        @if($pembayaran->metode_pembayaran)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($pembayaran->metode_pembayaran == 'tunai') bg-green-100 text-green-800
                                @elseif($pembayaran->metode_pembayaran == 'transfer') bg-blue-100 text-blue-800
                                @elseif($pembayaran->metode_pembayaran == 'online_payment') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst(str_replace(['_', '-'], ' ', $pembayaran->metode_pembayaran)) }}
                            </span>
                        @else
                            <span class="text-gray-400">Tidak diketahui</span>
                        @endif
                    </span>
                </div>
                
                <!-- Perbaikan: Gunakan strtolower untuk konsistensi pengecekan status -->
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Status:</span>
                    <span class="text-sm">
                        @if(strtolower($pembayaran->status) == 'lunas')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Lunas
                            </span>
                        @elseif(strtolower($pembayaran->status) == 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i>
                                Pending
                            </span>
                        @elseif(strtolower($pembayaran->status) == 'failed')
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
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Status Verifikasi:</span>
                    <span class="text-sm">
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

        <!-- Informasi Tagihan -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <i class="fas fa-file-invoice mr-2 text-green-600"></i>
                Informasi Tagihan
                {{-- Tombol Refresh --}}
                <button onclick="location.reload()" 
                        class="ml-auto text-xs bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded transition"
                        title="Refresh data">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </h3>
            
            @if($pembayaran->tagihan_id)
                @php
                    // Ambil data tagihan terbaru
                    $freshTagihan = \App\Models\Tagihan::with('meteran.user')->find($pembayaran->tagihan_id);
                @endphp
                
                @if($freshTagihan)
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">ID Tagihan:</span>
                        <span class="text-sm text-gray-900">#{{ $freshTagihan->id }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">Nomor Tagihan:</span>
                        <span class="text-sm text-gray-900">{{ $freshTagihan->nomor_tagihan ?? 'N/A' }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">Jumlah Tagihan:</span>
                        <span class="text-sm font-semibold text-gray-900">
                            Rp {{ number_format($freshTagihan->total_tagihan ?? $freshTagihan->jumlah ?? 0, 0, ',', '.') }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">Status Tagihan:</span>
                        <span class="text-sm">
                            @if($freshTagihan->status == 'sudah_bayar')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Sudah Bayar
                                </span>
                            @elseif($freshTagihan->status == 'belum_bayar')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Belum Bayar
                                </span>
                            @elseif($freshTagihan->status == 'menunggu_konfirmasi')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    Menunggu Konfirmasi
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-question-circle mr-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $freshTagihan->status ?? 'N/A')) }}
                                </span>
                            @endif
                        </span>
                    </div>
                    
                    @if($freshTagihan->periode)
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700">Periode:</span>
                            <span class="text-sm text-gray-900">{{ $freshTagihan->periode }}</span>
                        </div>
                    @endif
                    
                    @if($freshTagihan->meteran)
                        <div class="border-t pt-3 mt-3">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Data Pelanggan:</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-600">Nama:</span>
                                    <span class="text-xs text-gray-900">
                                        @if($freshTagihan->meteran->user)
                                            {{ $freshTagihan->meteran->user->name ?? $freshTagihan->meteran->user->nama ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-600">Email:</span>
                                    <span class="text-xs text-gray-900">
                                        {{ $freshTagihan->meteran->user->email ?? 'N/A' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-600">No. Meteran:</span>
                                    <span class="text-xs text-gray-900">{{ $freshTagihan->meteran->nomor_meteran ?? 'N/A' }}</span>
                                </div>
                                @if($freshTagihan->meteran->alamat)
                                    <div class="flex justify-between">
                                        <span class="text-xs text-gray-600">Alamat:</span>
                                        <span class="text-xs text-gray-900">{{ $freshTagihan->meteran->alamat }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                @else
                    <p class="text-sm text-gray-500 italic">Tagihan tidak ditemukan</p>
                @endif
            @else
                <p class="text-sm text-gray-500 italic">Informasi tagihan tidak tersedia</p>
            @endif
        </div>
    </div>

    <!-- Keterangan dan Catatan -->
    @if($pembayaran->keterangan || $pembayaran->catatan_admin)
        <div class="mt-6 bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <i class="fas fa-sticky-note mr-2 text-yellow-600"></i>
                Keterangan & Catatan
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($pembayaran->keterangan)
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Keterangan Pembayaran:</h4>
                        <p class="text-sm text-gray-900 bg-white p-3 rounded border">{{ $pembayaran->keterangan }}</p>
                    </div>
                @endif
                
                @if($pembayaran->catatan_admin)
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Catatan Admin:</h4>
                        <p class="text-sm text-gray-900 bg-white p-3 rounded border whitespace-pre-line">{{ $pembayaran->catatan_admin }}</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Informasi Sistem -->
    <div class="mt-6 bg-gray-50 p-6 rounded-lg">
        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
            <i class="fas fa-cogs mr-2 text-gray-600"></i>
            Informasi Sistem
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Dibuat pada:</span>
                    <span class="text-sm text-gray-900">
                        {{ $pembayaran->created_at ? $pembayaran->created_at->format('d F Y, H:i') : 'N/A' }}
                    </span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Diperbarui pada:</span>
                    <span class="text-sm text-gray-900">
                        {{ $pembayaran->updated_at ? $pembayaran->updated_at->format('d F Y, H:i') : 'N/A' }}
                    </span>
                </div>
                
                @if($pembayaran->processed_by)
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">Diproses oleh:</span>
                        <span class="text-sm text-gray-900">{{ $pembayaran->processed_by }}</span>
                    </div>
                @endif
                
                @if($pembayaran->processed_at)
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">Diproses pada:</span>
                        <span class="text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($pembayaran->processed_at)->format('d F Y, H:i') }}
                        </span>
                    </div>
                @endif
            </div>
            
            @if($pembayaran->is_verified)
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">Diverifikasi oleh:</span>
                        <span class="text-sm text-gray-900">
                            @if($pembayaran->verifiedBy)
                                {{ $pembayaran->verifiedBy->name ?? 'Admin ID: ' . $pembayaran->verified_by }}
                            @else
                                Admin ID: {{ $pembayaran->verified_by }}
                            @endif
                        </span>
                    </div>
                    
                    @if($pembayaran->verified_at)
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700">Diverifikasi pada:</span>
                            <span class="text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($pembayaran->verified_at)->format('d F Y, H:i') }}
                            </span>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Tombol Aksi -->
    <div class="mt-6 flex justify-end space-x-3 pt-6 border-t border-gray-200">
        {{-- Tombol Tolak Pembayaran --}}
        @if(!$pembayaran->is_verified && in_array($pembayaran->status, ['pending', 'lunas']))
            <a href="{{ route('admin.pembayaran.reject', $pembayaran->id) }}" 
               class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-times mr-2"></i> Tolak Pembayaran
            </a>
        @endif

        {{-- Tombol Quick Confirm --}}
        @if(!$pembayaran->is_verified && $pembayaran->status == 'lunas')
            <form action="{{ route('admin.pembayaran.confirm', $pembayaran->id) }}" 
                  method="POST" 
                  class="inline"
                  onsubmit="return confirm('Konfirmasi pembayaran ini tanpa checklist?\n\nUntuk verifikasi menyeluruh, gunakan tombol Konfirmasi Pembayaran.')">
                @csrf
                <input type="hidden" name="admin_note" value="Quick confirm tanpa form checklist">
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center"
                        title="Quick Confirm">
                    <i class="fas fa-check mr-2"></i> Quick Confirm
                </button>
            </form>
        @endif
        
        {{-- Tombol Hapus --}}
        @if(!($pembayaran->status == 'lunas' && $pembayaran->is_verified))
            <form action="{{ route('admin.pembayaran.destroy', $pembayaran->id) }}" 
                  method="POST" 
                  class="inline"
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus pembayaran ini?\n\nStatus tagihan akan dikembalikan ke belum bayar.')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
                    <i class="fas fa-trash mr-2"></i> Hapus
                </button>
            </form>
        @endif
    </div>
</div>

{{-- Skrip Auto-refresh untuk sinkronisasi status --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cek inkonsistensi status dan auto-refresh setelah 2 detik jika ada peringatan
    const warningAlert = document.querySelector('.bg-yellow-100');
    if (warningAlert && warningAlert.textContent.includes('status tagihan belum terupdate')) {
        setTimeout(function() {
            console.log('Melakukan refresh otomatis untuk sinkronisasi status...');
            location.reload();
        }, 2000);
    }
    
    // Sembunyikan pesan sukses setelah 5 detik
    const successAlert = document.querySelector('.bg-green-100');
    if (successAlert) {
        setTimeout(function() {
            successAlert.style.opacity = '0';
            setTimeout(function() {
                successAlert.style.display = 'none';
            }, 300);
        }, 5000);
    }
});
</script>
@endpush
@endsection