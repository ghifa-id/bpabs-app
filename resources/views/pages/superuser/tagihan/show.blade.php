@extends('layouts.app')

@section('title', 'Detail Tagihan')

@section('header', 'Detail Tagihan')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Tagihan</h2>
        <div class="flex space-x-2">
            <a href="{{ route('superuser.tagihan.edit', $tagihan->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <a href="{{ route('superuser.tagihan.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card Informasi Tagihan -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-file-invoice mr-2 text-blue-500"></i>
                Informasi Tagihan
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">ID Tagihan</label>
                    <p class="text-gray-800 font-medium">{{ $tagihan->id }}</p>
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
                    <p class="text-gray-800">{{ $tagihan->tanggal_tagihan ? \Carbon\Carbon::parse($tagihan->tanggal_tagihan)->format('d M Y') : '-' }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Jatuh Tempo</label>
                    <p class="text-gray-800 font-medium">{{ $tagihan->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->format('d M Y') : '-' }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
                    <div class="inline-block">
                        @switch($tagihan->status)
                            @case('belum_bayar')
                                <span class="px-3 py-2 bg-yellow-100 text-yellow-800 rounded-lg text-sm font-medium">
                                    <i class="fas fa-clock mr-1"></i> Belum Bayar
                                </span>
                                @break
                            @case('sudah_bayar')
                                <span class="px-3 py-2 bg-green-100 text-green-800 rounded-lg text-sm font-medium">
                                    <i class="fas fa-check-circle mr-1"></i> Sudah Bayar
                                </span>
                                @break
                            @case('terlambat')
                                <span class="px-3 py-2 bg-red-100 text-red-800 rounded-lg text-sm font-medium">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Terlambat
                                </span>
                                @break
                            @case('menunggu_konfirmasi')
                                <span class="px-3 py-2 bg-blue-100 text-blue-800 rounded-lg text-sm font-medium">
                                    <i class="fas fa-hourglass-half mr-1"></i> Menunggu Konfirmasi
                                </span>
                                @break
                            @default
                                <span class="px-3 py-2 bg-gray-100 text-gray-800 rounded-lg text-sm font-medium">
                                    {{ ucfirst($tagihan->status) }}
                                </span>
                        @endswitch
                    </div>
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
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nama Pelanggan</label>
                    <p class="text-gray-800 font-medium text-lg">{{ $tagihan->user->name ?? $tagihan->user->nama }}</p>
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
                    <label class="block text-sm font-medium text-gray-600 mb-1">Alamat</label>
                    <div class="bg-white p-3 rounded-lg border">
                        <p class="text-gray-800">{{ $tagihan->user->alamat ?? '-' }}</p>
                    </div>
                </div>
            </div>
            @else
            <div class="text-center py-4">
                <i class="fas fa-exclamation-triangle text-gray-400 text-3xl mb-2"></i>
                <p class="text-gray-500">Data pelanggan tidak ditemukan</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Card Informasi Meteran -->
    @if($tagihan->meteran)
    <div class="mt-6 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-tachometer-alt mr-2 text-purple-500"></i>
            Informasi Meteran
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Nomor Meteran</label>
                <p class="text-gray-800 font-bold">{{ $tagihan->meteran->nomor_meteran }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Status Meteran</label>
                <div class="inline-block">
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
            
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Lokasi</label>
                <p class="text-gray-800">{{ $tagihan->meteran->alamat ?? '-' }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Card Detail Pemakaian -->
    <div class="mt-6 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-line mr-2 text-indigo-500"></i>
            Detail Pemakaian
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="text-center">
                    <div class="bg-blue-100 p-3 rounded-full w-12 h-12 mx-auto mb-3 flex items-center justify-center">
                        <i class="fas fa-play text-blue-600"></i>
                    </div>
                    <p class="text-sm text-gray-600 mb-1">Meter Awal</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($tagihan->meter_awal, 0) }}</p>
                    <p class="text-xs text-gray-500">m³</p>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="text-center">
                    <div class="bg-green-100 p-3 rounded-full w-12 h-12 mx-auto mb-3 flex items-center justify-center">
                        <i class="fas fa-stop text-green-600"></i>
                    </div>
                    <p class="text-sm text-gray-600 mb-1">Meter Akhir</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($tagihan->meter_akhir, 0) }}</p>
                    <p class="text-xs text-gray-500">m³</p>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="text-center">
                    <div class="bg-purple-100 p-3 rounded-full w-12 h-12 mx-auto mb-3 flex items-center justify-center">
                        <i class="fas fa-tint text-purple-600"></i>
                    </div>
                    <p class="text-sm text-gray-600 mb-1">Total Pemakaian</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($tagihan->jumlah_pemakaian, 0) }}</p>
                    <p class="text-xs text-gray-500">m³</p>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="text-center">
                    <div class="bg-orange-100 p-3 rounded-full w-12 h-12 mx-auto mb-3 flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-orange-600"></i>
                    </div>
                    <p class="text-sm text-gray-600 mb-1">Tarif per m³</p>
                    <p class="text-lg font-bold text-orange-600">{{ number_format($tagihan->tarif_per_kubik, 0) }}</p>
                    <p class="text-xs text-gray-500">Rupiah</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Rincian Biaya -->
    <div class="mt-6 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-calculator mr-2 text-green-500"></i>
            Rincian Biaya
        </h3>
        
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
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
                
                <div class="p-4 bg-green-50 flex justify-between items-center">
                    <div>
                        <p class="text-lg font-bold text-green-800">Total Tagihan</p>
                        <p class="text-sm text-green-600">Total yang harus dibayar</p>
                    </div>
                    <p class="text-2xl font-bold text-green-800">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Keterangan -->
    @if($tagihan->keterangan)
    <div class="mt-6 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-sticky-note mr-2 text-yellow-500"></i>
            Keterangan
        </h3>
        
        <div class="bg-white p-4 rounded-lg border">
            <p class="text-gray-800">{{ $tagihan->keterangan }}</p>
        </div>
    </div>
    @endif

    <!-- Card Informasi Timestamp -->
    <div class="mt-6 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-clock mr-2 text-purple-500"></i>
            Informasi Waktu
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Dibuat pada</label>
                <p class="text-gray-800">{{ $tagihan->created_at ? $tagihan->created_at->format('d M Y, H:i') : '-' }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Terakhir diperbarui</label>
                <p class="text-gray-800">{{ $tagihan->updated_at ? $tagihan->updated_at->format('d M Y, H:i') : '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex justify-end space-x-2">
        <a href="{{ route('superuser.tagihan.delete', $tagihan->id) }}" 
           class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
            <i class="fas fa-trash mr-2"></i> Hapus Tagihan
        </a>
    </div>
</div>
@endsection