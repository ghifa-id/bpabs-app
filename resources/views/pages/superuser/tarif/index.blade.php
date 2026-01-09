@extends('layouts.app')

@section('title', 'Manajemen Tarif')

@section('header', 'Manajemen Tarif')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Daftar Tarif</h2>
        <a href="{{ route('superuser.tarif.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
            <i class="fas fa-plus mr-2"></i> Tambah Tarif
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg overflow-hidden">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="py-3 px-4 text-left font-semibold">No</th>
                    <th class="py-3 px-4 text-left font-semibold">Nama Tarif</th>
                    <th class="py-3 px-4 text-left font-semibold">Harga</th>
                    <th class="py-3 px-4 text-left font-semibold">Deskripsi</th>
                    <th class="py-3 px-4 text-left font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($tarifs as $index => $tarif)
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4">{{ $index + 1 }}</td>
                    <td class="py-3 px-4 font-medium text-gray-900">{{ $tarif->nama_tarif }}</td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                            Rp {{ number_format($tarif->harga, 0, ',', '.') }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-gray-600">{{ $tarif->deskripsi ?? '-' }}</td>
                    <td class="py-3 px-4">
                        <div class="flex space-x-2">
                            <a href="{{ route('superuser.tarif.show', $tarif->id) }}" 
                               class="text-green-600 hover:text-green-800" 
                               title="Lihat Detail Tarif">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('superuser.tarif.edit', $tarif->id) }}" 
                               class="text-blue-600 hover:text-blue-800" 
                               title="Edit Tarif">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('superuser.tarif.delete', $tarif->id) }}" 
                               class="text-red-600 hover:text-red-800" 
                               title="Hapus Tarif">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-6 px-4 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-money-bill-wave text-4xl text-gray-300 mb-3"></i>
                            <p class="text-lg font-medium mb-2">Belum ada data tarif</p>
                            <p class="text-sm mb-4">Klik tombol "Tambah Tarif" untuk menambahkan tarif baru.</p>
                            <a href="{{ route('superuser.tarif.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
                                <i class="fas fa-plus mr-2"></i> Tambah Tarif Pertama
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection