@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('header', 'Dashboard Admin')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 p-6">
        <div class="flex items-center">
            <div class="p-3 bg-blue-500 rounded-full">
                <i class="fas fa-users text-white text-2xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-gray-600 text-sm font-medium">Total Pelanggan</h2>
                <p class="text-2xl font-bold text-gray-800">{{ $totalPelanggan }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 p-6">
        <div class="flex items-center">
            <div class="p-3 bg-green-500 rounded-full">
                <i class="fas fa-money-bill text-white text-2xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-gray-600 text-sm font-medium">Pembayaran Bulan Ini</h2>
                <p class="text-2xl font-bold text-gray-800">{{ $pembayaranBulanIni }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 p-6">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-500 rounded-full">
                <i class="fas fa-clock text-white text-2xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-gray-600 text-sm font-medium">Menunggu Pembayaran</h2>
                <p class="text-2xl font-bold text-gray-800">{{ $menungguPembayaran }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 p-6">
        <div class="flex items-center">
            <div class="p-3 bg-red-500 rounded-full">
                <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-gray-600 text-sm font-medium">Tagihan Terlambat</h2>
                <p class="text-2xl font-bold text-gray-800">{{ $tagihanTerlambat }}</p>
            </div>
        </div>
    </div>
</div>

<div class="mt-8 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Pembayaran Terbaru</h2>
            <a href="{{ route('admin.pembayaran.index') }}" class="text-blue-500 hover:text-blue-600 text-sm font-medium">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($pembayaranTerbaru as $pembayaran)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($pembayaran->tagihan && $pembayaran->tagihan->meteran && $pembayaran->tagihan->meteran->user)
                                {{ $pembayaran->tagihan->meteran->user->name ?? $pembayaran->tagihan->meteran->user->nama }}
                            @elseif($pembayaran->tagihan && $pembayaran->tagihan->pembacaanMeteran && $pembayaran->tagihan->pembacaanMeteran->meteran && $pembayaran->tagihan->pembacaanMeteran->meteran->user)
                                {{ $pembayaran->tagihan->pembacaanMeteran->meteran->user->name ?? $pembayaran->tagihan->pembacaanMeteran->meteran->user->nama }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($pembayaran->tanggal_pembayaran)
                                {{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">Rp {{ number_format($pembayaran->jumlah_bayar ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @php
                                $status = $pembayaran->status ?? 'pending';
                                $bgColor = 'bg-yellow-100 text-yellow-800';
                                
                                if ($status == 'lunas') {
                                    $bgColor = 'bg-green-100 text-green-800';
                                } elseif (in_array($status, ['failed', 'expired', 'cancelled'])) {
                                    $bgColor = 'bg-red-100 text-red-800';
                                }
                            @endphp
                            
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $bgColor }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-center text-gray-500 text-sm">
                            Belum ada pembayaran terbaru
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection