@extends('layouts.app')

@section('title', 'Dashboard Pelanggan')
@section('header', 'Dashboard Pelanggan')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-blue-500 rounded-full">
                <i class="fas fa-tachometer-alt text-white text-2xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-gray-600">Penggunaan {{ $tagihanBulanIni ? $tagihanBulanIni->bulan . ' ' . $tagihanBulanIni->tahun : 'Bulan Ini' }}</h2>
                <p class="text-2xl font-bold">{{ number_format($penggunaanBulanIni, 0) }} m³</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-green-500 rounded-full">
                <i class="fas fa-file-invoice text-white text-2xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-gray-600">Tagihan {{ $tagihanBulanIni ? $tagihanBulanIni->bulan . ' ' . $tagihanBulanIni->tahun : 'Bulan Ini' }}</h2>
                <p class="text-2xl font-bold">Rp {{ $tagihanBulanIni ? number_format($tagihanBulanIni->total_tagihan, 0, ',', '.') : 0 }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 
                @if($statusPembayaran == 'Lunas') 
                    bg-green-500 
                @elseif($statusPembayaran == 'Terlambat') 
                    bg-red-500 
                @else 
                    bg-yellow-500 
                @endif 
                rounded-full">
                <i class="fas fa-calendar text-white text-2xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-gray-600">Status Pembayaran</h2>
                <p class="text-2xl font-bold 
                    @if($statusPembayaran == 'Lunas') 
                        text-green-600 
                    @elseif($statusPembayaran == 'Terlambat') 
                        text-red-600 
                    @else 
                        text-yellow-600 
                    @endif">
                    {{ $statusPembayaran }}
                </p>
            </div>
        </div>
    </div>
</div>

<div class="mt-8 bg-white rounded-lg shadow">
    <div class="p-6">
        <h2 class="text-xl font-bold mb-4">Riwayat Tagihan</h2>
        <div class="overflow-x-auto">
            @if($riwayatPembayaran->count() > 0)
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left">Periode</th>
                            <th class="px-4 py-2 text-left">Penggunaan</th>
                            <th class="px-4 py-2 text-left">Tagihan</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Tanggal Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayatPembayaran as $riwayat)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium">{{ $riwayat->periode }}</td>
                            <td class="px-4 py-2">{{ number_format($riwayat->penggunaan, 0) }} m³</td>
                            <td class="px-4 py-2 font-semibold">Rp {{ number_format($riwayat->tagihan, 0, ',', '.') }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded text-sm font-medium
                                    @if($riwayat->status == 'Lunas') 
                                        bg-green-100 text-green-800
                                    @elseif($riwayat->status == 'Terlambat') 
                                        bg-red-100 text-red-800
                                    @else 
                                        bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ $riwayat->status }}
                                </span>
                            </td>
                            <td class="px-4 py-2">{{ $riwayat->tanggal_bayar ?: '-' }}</td>
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
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Riwayat Tagihan</h3>
                    <p class="text-gray-500">Riwayat tagihan akan muncul setelah admin membuat tagihan untuk Anda.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection