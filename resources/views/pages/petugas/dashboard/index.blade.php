@extends('layouts.app')

@section('title', 'Dashboard Petugas')
@section('header', 'Dashboard Petugas')
@section('subtitle', 'Monitoring pembacaan meteran dan aktivitas lapangan')

@section('content')
<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Pembacaan Hari Ini (Global) -->
    <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-blue-500 rounded-full">
                <i class="fas fa-calendar-day text-white text-2xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-gray-600 text-sm font-medium">Pembacaan Hari Ini</h2>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($pembacaanHariIni, 0) }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    Target: {{ number_format($targetHarian, 0) }} meteran
                </p>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Status Target:</span>
                <span class="font-medium 
                    {{ $statusTarget == 'Tercapai' ? 'text-green-600' : 'text-yellow-600' }}">
                    {{ $statusTarget }}
                </span>
            </div>
            @if($totalPetugas > 0)
            <div class="text-xs text-gray-500 mt-1">
                Rata-rata: {{ $rataRataPembacaanHarian }}/petugas
            </div>
            @endif
        </div>
    </div>

    <!-- Pembacaan Bulan Ini (Global) -->
    <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-green-500 rounded-full">
                <i class="fas fa-calendar-alt text-white text-2xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-gray-600 text-sm font-medium">Pembacaan Bulan Ini</h2>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($pembacaanBulanIni, 0) }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ date('F Y') }}
                </p>
            </div>
        </div>
        <div class="mt-4">
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-500 h-2 rounded-full transition-all duration-300" 
                     style="width: {{ min($progressPembacaan, 100) }}%"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-600 mt-1">
                <span>Progress</span>
                <span>{{ $progressPembacaan }}%</span>
            </div>
        </div>
    </div>

    <!-- Meteran Belum Dibaca -->
    <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow p-6">
        <div class="flex items-center">
            <div class="p-3 
                {{ $meteranBelumBaca > 0 ? 'bg-orange-500' : 'bg-gray-500' }} 
                rounded-full">
                <i class="fas fa-tachometer-alt text-white text-2xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-gray-600 text-sm font-medium">Belum Dibaca</h2>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($meteranBelumBaca, 0) }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    dari {{ number_format($totalMeteranAktif, 0) }} meteran aktif
                </p>
            </div>
        </div>
        @if($meteranBelumBaca > 0)
        <div class="mt-4">
            <a href="{{ route('petugas.meteran.index') }}" 
               class="inline-flex items-center text-sm text-orange-600 hover:text-orange-700 font-medium">
                <i class="fas fa-arrow-right mr-1"></i>
                Mulai Baca Meteran
            </a>
        </div>
        @endif
    </div>

    <!-- Pembacaan Minggu Ini (Global) -->
    <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-purple-500 rounded-full">
                <i class="fas fa-chart-line text-white text-2xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-gray-600 text-sm font-medium">Minggu Ini</h2>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($pembacaanMingguIni, 0) }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    Rata-rata {{ number_format($pembacaanMingguIni / 7, 1) }}/hari
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold mb-2">Baca Meteran</h3>
                <p class="text-blue-100 text-sm mb-4">Mulai pembacaan meteran pelanggan</p>
                <a href="{{ route('petugas.meteran.index') }}" 
                   class="inline-flex items-center bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-blue-50 transition-colors">
                    <i class="fas fa-tachometer-alt mr-2"></i>
                    Mulai Pembacaan
                </a>
            </div>
            <div class="text-6xl text-blue-300 opacity-50">
                <i class="fas fa-tachometer-alt"></i>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold mb-2">Riwayat</h3>
                <p class="text-purple-100 text-sm mb-4">Lihat riwayat pembacaan</p>
                <a href="{{ route('petugas.pembacaan.index') }}" 
                   class="inline-flex items-center bg-white text-purple-600 px-4 py-2 rounded-lg font-medium hover:bg-purple-50 transition-colors">
                    <i class="fas fa-history mr-2"></i>
                    Lihat Riwayat
                </a>
            </div>
            <div class="text-6xl text-purple-300 opacity-50">
                <i class="fas fa-history"></i>
            </div>
        </div>
    </div>
</div>

<!-- Riwayat Pembacaan Terbaru -->
<div class="bg-white rounded-lg shadow">
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Riwayat Pembacaan Terbaru</h2>
            <a href="{{ route('petugas.pembacaan.index') }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                Lihat Semua
                <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="overflow-x-auto">
            @if($riwayatPembacaan->count() > 0)
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal & Waktu
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                No. Meteran
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pelanggan
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pembacaan
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pemakaian
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($riwayatPembacaan as $riwayat)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $riwayat->tanggal }}</div>
                                <div class="text-sm text-gray-500">{{ $riwayat->waktu }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $riwayat->nomor_meteran }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900">{{ $riwayat->pelanggan }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ number_format($riwayat->meter_awal) }} → {{ number_format($riwayat->meter_akhir) }}
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-blue-600">
                                    {{ number_format($riwayat->pemakaian) }} m³
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($riwayat->status == 'selesai') 
                                        bg-green-100 text-green-800
                                    @elseif($riwayat->status == 'pending') 
                                        bg-yellow-100 text-yellow-800
                                    @else 
                                        bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($riwayat->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="bg-gray-100 rounded-full p-4 w-16 h-16 mx-auto mb-4">
                        <i class="fas fa-tachometer-alt text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Pembacaan</h3>
                    <p class="text-gray-500 mb-4">Mulai melakukan pembacaan meteran untuk melihat riwayat di sini.</p>
                    <a href="{{ route('petugas.meteran.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Mulai Pembacaan Meteran
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Team Performance Indicator -->
@if($pembacaanHariIni > 0 || $pembacaanBulanIni > 0)
<div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-blue-600 text-xl"></i>
            </div>
        </div>
        <div class="ml-4 flex-1">
            <h3 class="text-lg font-semibold text-blue-900">Kinerja Tim Hari Ini</h3>
            <p class="text-blue-700">
                @if($statusTarget == 'Tercapai')
                    🎉 Tim petugas telah mencapai target pembacaan hari ini!
                @else
                    💪 Mari bersama-sama mencapai target pembacaan hari ini.
                @endif
                @if($totalPetugas > 0)
                    Tim terdiri dari {{ $totalPetugas }} petugas aktif.
                @endif
            </p>
        </div>
        @if($targetHarian > 0)
        <div class="flex-shrink-0 text-right">
            <div class="text-2xl font-bold text-blue-900">
                {{ round(($pembacaanHariIni / $targetHarian) * 100) }}%
            </div>
            <div class="text-sm text-blue-600">dari target</div>
        </div>
        @endif
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    // Auto refresh data setiap 5 menit
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            location.reload();
        }
    }, 300000); // 5 menit

    // Notification untuk reminder pembacaan
    @if($meteranBelumBaca > 0 && $pembacaanHariIni < $targetHarian)
    setTimeout(function() {
        if (Notification.permission === "granted") {
            new Notification("BPABS - Reminder", {
                body: "Masih ada {{ $meteranBelumBaca }} meteran yang belum dibaca bulan ini.",
                icon: "{{ asset('assets/img/logo.png') }}"
            });
        }
    }, 5000);
    @endif
</script>
@endpush