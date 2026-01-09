@extends('layouts.app')

@section('title', 'Detail Tagihan')

@section('header', 'Detail Tagihan')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Detail Tagihan</h2>
                    <p class="text-sm text-gray-600 mt-1">Informasi lengkap tagihan pelanggan</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <!-- Status Sync Button -->
                    <a href="{{ route('admin.tagihan.check-status-consistency', $tagihan->id) }}" 
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center text-sm"
                    onclick="checkStatusConsistency({{ $tagihan->id }}); return false;">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Cek Status
                    </a>
                    
                    <!-- Edit Button -->
                    <a href="{{ route('admin.tagihan.edit', $tagihan->id) }}" 
                       class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit
                    </a>
                    
                    <!-- Back Button -->
                    <a href="{{ route('admin.tagihan.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="mx-6 mt-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <p>{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mx-6 mt-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <p>{{ session('error') }}</p>
            </div>
        </div>
        @endif

        @if(session('info'))
        <div class="mx-6 mt-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded" role="alert">
            <div class="flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                <p>{{ session('info') }}</p>
            </div>
        </div>
        @endif

        <!-- Main Content -->
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Card Informasi Tagihan -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-file-invoice mr-2 text-blue-500"></i>
                        Informasi Tagihan
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">ID Tagihan</label>
                            <p class="text-gray-800 font-medium">#{{ $tagihan->id }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Nomor Tagihan</label>
                            <p class="text-gray-800 font-bold text-lg">{{ $tagihan->nomor_tagihan ?? '-' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Periode</label>
                            <p class="text-gray-800 font-bold text-lg">{{ $tagihan->bulan }} {{ $tagihan->tahun }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Tagihan</label>
                            <p class="text-gray-800">{{ $tagihan->tanggal_tagihan ? \Carbon\Carbon::parse($tagihan->tanggal_tagihan)->format('d M Y, H:i') : '-' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Jatuh Tempo</label>
                            <p class="text-gray-800 font-medium">
                                {{ $tagihan->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->format('d M Y') : '-' }}
                                @if($tagihan->tanggal_jatuh_tempo && \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->isPast() && $tagihan->status !== \App\Models\Tagihan::STATUS_SUDAH_BAYAR)
                                    <span class="ml-2 text-red-600 text-sm font-medium">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Terlambat {{ \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->diffForHumans() }}
                                    </span>
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
                            <div class="flex flex-col space-y-2">
                                @switch($tagihan->status)
                                    @case(\App\Models\Tagihan::STATUS_BELUM_BAYAR)
                                        <span class="inline-flex items-center px-3 py-2 bg-yellow-100 text-yellow-800 rounded-lg text-sm font-medium w-fit">
                                            <i class="fas fa-clock mr-2"></i> Belum Bayar
                                        </span>
                                        @break
                                    @case(\App\Models\Tagihan::STATUS_SUDAH_BAYAR)
                                        <span class="inline-flex items-center px-3 py-2 bg-green-100 text-green-800 rounded-lg text-sm font-medium w-fit">
                                            <i class="fas fa-check-circle mr-2"></i> Sudah Bayar
                                        </span>
                                        @break
                                    @case(\App\Models\Tagihan::STATUS_TERLAMBAT)
                                        <span class="inline-flex items-center px-3 py-2 bg-red-100 text-red-800 rounded-lg text-sm font-medium w-fit">
                                            <i class="fas fa-exclamation-triangle mr-2"></i> Terlambat
                                        </span>
                                        @break
                                    @case(\App\Models\Tagihan::STATUS_MENUNGGU_KONFIRMASI)
                                        <span class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-800 rounded-lg text-sm font-medium w-fit">
                                            <i class="fas fa-hourglass-half mr-2"></i> Menunggu Konfirmasi
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-800 rounded-lg text-sm font-medium w-fit">
                                            <i class="fas fa-question mr-2"></i> {{ ucfirst(str_replace('_', ' ', $tagihan->status)) }}
                                        </span>
                                @endswitch

                                <!-- Manual Status Info -->
                                @if($tagihan->is_manual_status ?? false)
                                <div class="mt-2 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                                    <div class="flex items-start text-orange-800 text-sm">
                                        <i class="fas fa-hand-paper mr-2 mt-0.5 flex-shrink-0"></i>
                                        <div class="flex-1">
                                            <strong class="block">Status diubah manual oleh admin</strong>
                                            <div class="text-xs text-orange-600 mt-1">
                                                @if($tagihan->manualUpdatedBy ?? false)
                                                    Oleh: {{ $tagihan->manualUpdatedBy->name ?? 'N/A' }}
                                                @endif
                                                @if($tagihan->manual_updated_at ?? false)
                                                    pada {{ \Carbon\Carbon::parse($tagihan->manual_updated_at)->format('d/m/Y H:i') }}
                                                @endif
                                            </div>
                                            <div class="mt-2">
                                                <form method="POST" action="{{ route('admin.tagihan.reset-manual-status', $tagihan->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="text-xs bg-orange-600 text-white px-2 py-1 rounded hover:bg-orange-700 transition-colors duration-200"
                                                        onclick="return confirm('Reset status manual? Sistem akan otomatis menyesuaikan status berdasarkan pembayaran.')">
                                                    <i class="fas fa-undo mr-1"></i>
                                                    Reset Manual Status
                                                </button>
                                            </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Status Consistency Check Result -->
                        <div id="statusConsistencyResult" class="hidden">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Card Informasi Pelanggan -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-user mr-2 text-green-500"></i>
                        Informasi Pelanggan
                    </h3>
                    
                    @if($tagihan->user)
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-green-100 rounded-full p-2">
                                <i class="fas fa-user text-green-600"></i>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Nama Pelanggan</label>
                                <p class="text-gray-800 font-medium text-lg">{{ $tagihan->user->name ?? $tagihan->user->nama }}</p>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                            <p class="text-gray-800">{{ $tagihan->user->email }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">No. HP</label>
                            <p class="text-gray-800">{{ $tagihan->user->no_hp ?? $tagihan->user->telepon ?? '-' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Status Pelanggan</label>
                            <div class="inline-block">
                                @if($tagihan->user->status == 'active')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                        <i class="fas fa-check-circle mr-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                        <i class="fas fa-times-circle mr-1"></i> Nonaktif
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Alamat</label>
                            <div class="bg-white p-3 rounded-lg border">
                                <p class="text-gray-800">{{ $tagihan->user->alamat ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-500">Data pelanggan tidak ditemukan</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Card Informasi Meteran -->
    @if($tagihan->meteran)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-tachometer-alt mr-2 text-purple-500"></i>
            Informasi Meteran
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-lg mr-3">
                        <i class="fas fa-barcode text-purple-600"></i>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Nomor Meteran</label>
                        <p class="text-gray-800 font-bold text-lg">{{ $tagihan->meteran->nomor_meteran }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-lg mr-3">
                        <i class="fas fa-info-circle text-purple-600"></i>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Status Meteran</label>
                        <div class="mt-1">
                            @if($tagihan->meteran->status == 'aktif')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-check-circle mr-1"></i> Aktif
                                </span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-times-circle mr-1"></i> Nonaktif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-lg mr-3">
                        <i class="fas fa-map-marker-alt text-purple-600"></i>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Lokasi</label>
                        <p class="text-gray-800">{{ Str::limit($tagihan->meteran->alamat ?? '-', 30) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Card Detail Pemakaian -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-line mr-2 text-indigo-500"></i>
            Detail Pemakaian & Pembacaan Meteran
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                <div class="text-center">
                    <div class="bg-blue-100 p-3 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                        <i class="fas fa-play text-blue-600 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-600 mb-1">Meter Awal</p>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($tagihan->meter_awal, 0) }}</p>
                    <p class="text-xs text-gray-500">m³</p>
                </div>
            </div>
            
            <div class="bg-green-50 p-6 rounded-lg border border-green-200">
                <div class="text-center">
                    <div class="bg-green-100 p-3 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                        <i class="fas fa-stop text-green-600 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-600 mb-1">Meter Akhir</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($tagihan->meter_akhir, 0) }}</p>
                    <p class="text-xs text-gray-500">m³</p>
                </div>
            </div>
            
            <div class="bg-purple-50 p-6 rounded-lg border border-purple-200">
                <div class="text-center">
                    <div class="bg-purple-100 p-3 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                        <i class="fas fa-tint text-purple-600 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-600 mb-1">Total Pemakaian</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($tagihan->jumlah_pemakaian, 0) }}</p>
                    <p class="text-xs text-gray-500">m³</p>
                </div>
            </div>
            
            <div class="bg-orange-50 p-6 rounded-lg border border-orange-200">
                <div class="text-center">
                    <div class="bg-orange-100 p-3 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-orange-600 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-600 mb-1">Tarif per m³</p>
                    <p class="text-lg font-bold text-orange-600">{{ number_format($tagihan->tarif_per_kubik, 0) }}</p>
                    <p class="text-xs text-gray-500">Rupiah</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Rincian Biaya -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-calculator mr-2 text-green-500"></i>
            Rincian Biaya & Total Tagihan
        </h3>
        
        <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
            <div class="divide-y divide-gray-200">
                <div class="p-4 flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-800">Biaya Pemakaian</p>
                        <p class="text-xs text-gray-500">{{ number_format($tagihan->jumlah_pemakaian, 0) }} m³ × Rp {{ number_format($tagihan->tarif_per_kubik, 0) }}</p>
                    </div>
                    <p class="text-lg font-semibold text-gray-800">Rp {{ number_format($tagihan->biaya_pemakaian, 0, ',', '.') }}</p>
                </div>
                
                <div class="p-4 flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-800">Biaya Administrasi</p>
                        <p class="text-xs text-gray-500">Biaya tetap bulanan</p>
                    </div>
                    <p class="text-lg font-semibold text-gray-800">Rp {{ number_format($tagihan->biaya_admin, 0, ',', '.') }}</p>
                </div>
                
                @if($tagihan->biaya_beban > 0)
                <div class="p-4 flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-800">Biaya Beban</p>
                        <p class="text-xs text-gray-500">Biaya tambahan</p>
                    </div>
                    <p class="text-lg font-semibold text-gray-800">Rp {{ number_format($tagihan->biaya_beban, 0, ',', '.') }}</p>
                </div>
                @endif
                
                @if($tagihan->denda > 0)
                <div class="p-4 flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-red-600">Denda Keterlambatan</p>
                        <p class="text-xs text-red-500">Denda pembayaran terlambat</p>
                    </div>
                    <p class="text-lg font-semibold text-red-600">Rp {{ number_format($tagihan->denda, 0, ',', '.') }}</p>
                </div>
                @endif
                
                <div class="p-6 bg-green-50 flex justify-between items-center">
                    <div>
                        <p class="text-xl font-bold text-green-800">Total Tagihan</p>
                        <p class="text-sm text-green-600">Total yang harus dibayar</p>
                    </div>
                    <p class="text-3xl font-bold text-green-800">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Informasi Pembayaran -->
    @if($tagihan->pembayaran)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-credit-card mr-2 text-blue-500"></i>
            Informasi Pembayaran
        </h3>
        
        <div class="bg-blue-50 rounded-lg border border-blue-200 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">ID Pembayaran</label>
                        <p class="text-gray-800 font-bold">#{{ $tagihan->pembayaran->id }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nomor Pembayaran</label>
                        <p class="text-gray-800 font-medium">{{ $tagihan->pembayaran->nomor_pembayaran ?? '-' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Metode Pembayaran</label>
                        <p class="text-gray-800">{{ ucfirst($tagihan->pembayaran->metode_pembayaran) ?? '-' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Status Pembayaran</label>
                        @switch($tagihan->pembayaran->status)
                            @case('lunas')
                                <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                    <i class="fas fa-check-circle mr-1"></i> Lunas
                                </span>
                                @break
                            @case('pending')
                                <span class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                                    <i class="fas fa-clock mr-1"></i> Pending
                                </span>
                                @break
                            @case('failed')
                                <span class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                                    <i class="fas fa-times mr-1"></i> Gagal
                                </span>
                                @break
                            @default
                                <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-medium">
                                    {{ ucfirst($tagihan->pembayaran->status) }}
                                </span>
                        @endswitch
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Jumlah Bayar</label>
                        <p class="text-gray-800 font-bold text-lg">Rp {{ number_format($tagihan->pembayaran->jumlah, 0, ',', '.') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Pembayaran</label>
                        <p class="text-gray-800">{{ $tagihan->pembayaran->tanggal_pembayaran ? \Carbon\Carbon::parse($tagihan->pembayaran->tanggal_pembayaran)->format('d M Y, H:i') : '-' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Transaction ID</label>
                        <p class="text-gray-800 font-mono text-sm">{{ $tagihan->pembayaran->transaction_id ?? '-' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Status Verifikasi</label>
                        @if($tagihan->pembayaran->is_verified)
                            <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                <i class="fas fa-check-circle mr-1"></i> Terverifikasi
                            </span>
                            @if($tagihan->pembayaran->verified_at)
                                <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($tagihan->pembayaran->verified_at)->format('d M Y, H:i') }}</p>
                            @endif
                        @else
                            <span class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                                <i class="fas fa-clock mr-1"></i> Belum Diverifikasi
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            
            @if($tagihan->pembayaran->keterangan)
            <div class="mt-4 p-3 bg-white rounded border">
                <label class="block text-sm font-medium text-gray-600 mb-1">Keterangan</label>
                <p class="text-gray-800 text-sm">{{ $tagihan->pembayaran->keterangan }}</p>
            </div>
            @endif
            
            <!-- Pembayaran Actions -->
            @if(!$tagihan->pembayaran->is_verified && $tagihan->pembayaran->status === 'lunas')
            <div class="mt-4 pt-4 border-t border-blue-200">
                <div class="flex space-x-2">
                    <a href="{{ route('admin.pembayaran.show-confirm-form', $tagihan->pembayaran->id) }}" 
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center text-sm">
                        <i class="fas fa-check mr-2"></i>
                        Konfirmasi Pembayaran
                    </a>
                    <a href="{{ route('admin.pembayaran.show-reject-form', $tagihan->pembayaran->id) }}" 
                       class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center text-sm">
                        <i class="fas fa-times mr-2"></i>
                        Tolak Pembayaran
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-credit-card mr-2 text-gray-400"></i>
            Informasi Pembayaran
        </h3>
        
        <div class="text-center py-8 bg-gray-50 rounded-lg border border-gray-200">
            <i class="fas fa-receipt text-gray-400 text-4xl mb-4"></i>
            <h4 class="text-lg font-medium text-gray-600 mb-2">Belum Ada Pembayaran</h4>
            <p class="text-gray-500 mb-4">Tagihan ini belum memiliki catatan pembayaran</p>
            
            @if($tagihan->status !== \App\Models\Tagihan::STATUS_SUDAH_BAYAR)
            <a href="{{ route('admin.pembayaran.create', ['tagihan_id' => $tagihan->id]) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors duration-200 inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Tambah Pembayaran Manual
            </a>
            @endif
        </div>
    </div>
    @endif

    <!-- Card Keterangan -->
    @if($tagihan->keterangan)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-sticky-note mr-2 text-yellow-500"></i>
            Keterangan
        </h3>
        
        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
            <p class="text-gray-800">{{ $tagihan->keterangan }}</p>
        </div>
    </div>
    @endif

    <!-- Card Informasi Timestamp -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-clock mr-2 text-purple-500"></i>
            Informasi Waktu & Audit
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Dibuat pada</label>
                    <p class="text-gray-800">{{ $tagihan->created_at ? $tagihan->created_at->format('d M Y, H:i') : '-' }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Terakhir diperbarui</label>
                    <p class="text-gray-800">{{ $tagihan->updated_at ? $tagihan->updated_at->format('d M Y, H:i') : '-' }}</p>
                </div>
            </div>
            
            <div class="space-y-4">
                @if($tagihan->is_manual_status ?? false)
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Manual Status Update</label>
                    <p class="text-gray-800">{{ $tagihan->manual_updated_at ? \Carbon\Carbon::parse($tagihan->manual_updated_at)->format('d M Y, H:i') : '-' }}</p>
                </div>
                @endif
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Status Aktif</label>
                    @if($tagihan->is_active ?? true)
                        <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                            <i class="fas fa-check-circle mr-1"></i> Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                            <i class="fas fa-times-circle mr-1"></i> Nonaktif
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0 sm:space-x-3">
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                <!-- Create Payment Button -->
                @if(!$tagihan->pembayaran && $tagihan->status !== \App\Models\Tagihan::STATUS_SUDAH_BAYAR)
                <a href="{{ route('admin.pembayaran.create', ['tagihan_id' => $tagihan->id]) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Pembayaran
                </a>
                @endif
                
                <!-- Force Sync Button -->
                @if($tagihan->pembayaran)
                <a href="{{ route('admin.tagihan.force-sync-status', $tagihan->id) }}" 
                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center"
                onclick="return confirm('Yakin ingin melakukan sinkronisasi paksa status tagihan dan pembayaran?')">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Force Sync Status
                </a>
                @endif
            </div>
            
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                <!-- View Payment Button -->
                @if($tagihan->pembayaran)
                <a href="{{ route('admin.pembayaran.show', $tagihan->pembayaran->id) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-credit-card mr-2"></i>
                    Lihat Pembayaran
                </a>
                @endif
                
                <!-- Delete Button -->
                <a href="{{ route('admin.tagihan.delete', $tagihan->id) }}" 
                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-trash mr-2"></i>
                    Hapus Tagihan
                </a>
            </div>
        </div>
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

    // Status consistency check function
    function checkStatusConsistency(tagihanId) {
        const resultDiv = document.getElementById('statusConsistencyResult');
        
        // Show loading
        resultDiv.innerHTML = `
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center text-blue-800 text-sm">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    <span>Checking status consistency...</span>
                </div>
            </div>
        `;
        resultDiv.classList.remove('hidden');
        
        const url = `/admin/tagihan/${tagihanId}/check-status-consistency`;
        
        // Make AJAX request
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const isConsistent = data.data.is_consistent;
                    const currentStatus = data.data.current_tagihan_status;
                    const expectedStatus = data.data.expected_tagihan_status;
                    const pembayaranStatus = data.data.pembayaran_status || 'No payment';
                    const isVerified = data.data.is_verified || false;
                    const isManualStatus = data.data.is_manual_status || false;
                    
                    let statusClass = isConsistent ? 'green' : 'red';
                    let statusIcon = isConsistent ? 'check-circle' : 'exclamation-triangle';
                    let statusText = isConsistent ? 'Status konsisten' : 'Status tidak konsisten';
                    
                    // Special handling for manual status
                    if (isManualStatus) {
                        statusClass = 'orange';
                        statusIcon = 'hand-paper';
                        statusText = 'Status manual - konsistensi diabaikan';
                    }
                    
                    resultDiv.innerHTML = `
                        <div class="p-3 bg-${statusClass}-50 border border-${statusClass}-200 rounded-lg">
                            <div class="text-${statusClass}-800 text-sm space-y-2">
                                <div class="flex items-center font-medium">
                                    <i class="fas fa-${statusIcon} mr-2"></i>
                                    <span>${statusText}</span>
                                </div>
                                <div class="text-xs space-y-1">
                                    <div><strong>Status Tagihan:</strong> ${currentStatus}</div>
                                    <div><strong>Status yang Diharapkan:</strong> ${expectedStatus}</div>
                                    <div><strong>Status Pembayaran:</strong> ${pembayaranStatus}</div>
                                    <div><strong>Terverifikasi:</strong> ${isVerified ? 'Ya' : 'Tidak'}</div>
                                    <div><strong>Manual Status:</strong> ${isManualStatus ? 'Ya' : 'Tidak'}</div>
                                    <div><strong>Pengecekan:</strong> ${data.data.last_checked}</div>
                                </div>
                                ${!isConsistent && !isManualStatus ? `
                                    <div class="mt-2">
                                        <button onclick="location.reload()" 
                                                class="text-xs bg-${statusClass}-600 text-white px-2 py-1 rounded hover:bg-${statusClass}-700">
                                            <i class="fas fa-sync-alt mr-1"></i>
                                            Refresh Halaman
                                        </button>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center text-red-800 text-sm">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <span>Error: ${data.message}</span>
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Status check error:', error);
                resultDiv.innerHTML = `
                    <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center text-red-800 text-sm">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <span>Gagal melakukan pengecekan status. Check console for details.</span>
                        </div>
                    </div>
                `;
            });
        
        // Hide result after 15 seconds
        setTimeout(() => {
            resultDiv.style.transition = 'opacity 0.5s ease-out';
            resultDiv.style.opacity = '0';
            setTimeout(() => {
                resultDiv.classList.add('hidden');
                resultDiv.style.opacity = '1';
            }, 500);
        }, 15000);
    }

    // Confirm delete actions
    document.querySelectorAll('a[href*="delete"], a[href*="hapus"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            if (!confirm('Apakah Anda yakin ingin menghapus tagihan ini? Tindakan ini tidak dapat dibatalkan.')) {
                e.preventDefault();
            }
        });
    });

    // Confirm force sync actions
    document.querySelectorAll('a[href*="force-sync"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            if (!confirm('Yakin ingin melakukan sinkronisasi paksa? Ini akan mengubah status berdasarkan data pembayaran aktual.')) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* Custom transitions for status badges */
    .status-badge {
        transition: all 0.3s ease;
    }
    
    .status-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    /* Card hover effects */
    .bg-white.rounded-lg.shadow-md {
        transition: box-shadow 0.3s ease;
    }
    
    .bg-white.rounded-lg.shadow-md:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    /* Button hover animations */
    .bg-blue-600:hover, .bg-green-600:hover, .bg-yellow-600:hover, 
    .bg-red-600:hover, .bg-purple-600:hover, .bg-gray-500:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    /* Smooth loading animation */
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    /* Status consistency result animations */
    #statusConsistencyResult {
        transition: all 0.3s ease;
    }
    
    /* Manual status info styling */
    .bg-orange-50 {
        background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        .grid-cols-1.md\\:grid-cols-2, 
        .grid-cols-1.md\\:grid-cols-3,
        .grid-cols-1.md\\:grid-cols-4 {
            grid-template-columns: 1fr;
        }
        
        .text-2xl {
            font-size: 1.5rem;
        }
        
        .text-3xl {
            font-size: 1.875rem;
        }
        
        .px-6 {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }
    
    /* Print styles */
    @media print {
        .bg-blue-600, .bg-green-600, .bg-yellow-600, .bg-red-600, .bg-purple-600 {
            background-color: #374151 !important;
        }
        
        .shadow-md {
            box-shadow: none !important;
            border: 1px solid #e5e7eb;
        }
    }
</style>
@endpush
@endsection