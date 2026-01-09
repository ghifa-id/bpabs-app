@extends('layouts.app')

@section('title', 'Detail Pembacaan Meteran')
@section('header', 'Detail Pembacaan Meteran')
@section('subtitle', 'Riwayat pembacaan meteran pelanggan')

@section('content')
<div class="bg-white rounded-lg shadow-md">
    <!-- Header Section -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Riwayat Pembacaan Meteran</h2>
                <p class="text-sm text-gray-600 mt-1">Detail pembacaan untuk {{ $meteran->nomor_meteran }}</p>
            </div>
            <a href="{{ route('petugas.meteran.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Meteran Info -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-full p-3 mr-4">
                    <i class="fas fa-tachometer-alt text-blue-600 text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">{{ $meteran->nomor_meteran }}</h3>
                    <p class="text-sm text-gray-500">Status: 
                        <span class="{{ $meteran->status == 'aktif' ? 'text-green-600' : 'text-red-600' }}">
                            {{ ucfirst($meteran->status) }}
                        </span>
                    </p>
                </div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-sm text-gray-700">
                    <p><strong>Pelanggan:</strong> {{ $meteran->user->name ?? 'N/A' }}</p>
                    <p><strong>Email:</strong> {{ $meteran->user->email ?? 'N/A' }}</p>
                    <p><strong>Alamat:</strong> {{ $meteran->user->alamat ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pembacaan Table -->
    <div class="p-6">
        @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
            <p>{{ session('success') }}</p>
        </div>
        @endif

        @if($pembacaans->isEmpty())
        <div class="text-center py-8">
            <div class="bg-gray-100 rounded-full p-4 w-16 h-16 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-tachometer-alt text-gray-400 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Pembacaan</h3>
            <p class="text-gray-500">Tidak ada riwayat pembacaan untuk meteran ini</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold">No</th>
                        <th class="py-3 px-4 text-left font-semibold">Bulan</th>
                        <th class="py-3 px-4 text-left font-semibold">Tahun</th>
                        <th class="py-3 px-4 text-left font-semibold">Meter Awal</th>
                        <th class="py-3 px-4 text-left font-semibold">Meter Akhir</th>
                        <th class="py-3 px-4 text-left font-semibold">Pemakaian</th>
                        <th class="py-3 px-4 text-left font-semibold">Tanggal Baca</th>
                        <th class="py-3 px-4 text-left font-semibold">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($pembacaans as $index => $pembacaan)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $index + 1 }}</td>
                        <td class="py-3 px-4">{{ $pembacaan->bulan }}</td>
                        <td class="py-3 px-4">{{ $pembacaan->tahun }}</td>
                        <td class="py-3 px-4">{{ $pembacaan->meter_awal }} m³</td>
                        <td class="py-3 px-4">{{ $pembacaan->meter_akhir }} m³</td>
                        <td class="py-3 px-4 font-medium">
                            {{ $pembacaan->meter_akhir - $pembacaan->meter_awal }} m³
                        </td>
                        <td class="py-3 px-4">
                            {{ \Carbon\Carbon::parse($pembacaan->tanggal_meteran)->format('d/m/Y') }}
                        </td>
                        <td class="py-3 px-4">
                            {{ $pembacaan->petugas->name ?? 'N/A' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $pembacaans->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Auto-hide success message after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('[role="alert"]');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>
@endpush
@endsection