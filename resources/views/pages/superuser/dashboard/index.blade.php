@extends('layouts.app')

@section('title', 'Dashboard Superuser')
@section('header', 'Dashboard Superuser')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="container mx-auto mt-8 p-4">
        <h2 class="text-2xl font-bold mb-6">Dashboard Superuser</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700">Total Users</h3>
                <p class="text-3xl font-bold text-blue-600">{{ $totalUsers }}</p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700">Total Tagihan</h3>
                <p class="text-3xl font-bold text-green-600">{{ $totalTagihan }}</p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700">Pembayaran Success</h3>
                <p class="text-3xl font-bold text-purple-600">{{ $totalPembayaran }}</p>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Recent Pembayaran</h3>
            @if($recentPembayaran->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left">ID</th>
                                <th class="px-4 py-2 text-left">Tagihan</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPembayaran as $pembayaran)
                            <tr class="border-b">
                                <td class="px-4 py-2">{{ $pembayaran->id }}</td>
                                <td class="px-4 py-2">{{ $pembayaran->tagihan->nama ?? 'N/A' }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        {{ $pembayaran->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-2">{{ $pembayaran->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">Belum ada pembayaran.</p>
            @endif
        </div>
    </div>
</div>
@endsection