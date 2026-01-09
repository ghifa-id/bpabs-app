@extends('layouts.app')

@section('title', 'Manajemen Meteran')

@section('header', 'Manajemen Meteran')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Daftar Meteran</h2>
        <a href="{{ route('superuser.meteran.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
            <i class="fas fa-plus mr-2"></i> Tambah Meteran
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
                    <th class="py-3 px-4 text-left font-semibold">Nomor Meteran</th>
                    <th class="py-3 px-4 text-left font-semibold">Pelanggan</th>
                    <th class="py-3 px-4 text-left font-semibold">Status</th>
                    <th class="py-3 px-4 text-left font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($meterans as $index => $meteran)
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4">{{ $index + 1 }}</td>
                    <td class="py-3 px-4 font-medium text-gray-900">{{ $meteran->nomor_meteran }}</td>
                    <td class="py-3 px-4">{{ $meteran->user->nama ?? $meteran->user->name }}</td>
                    <td class="py-3 px-4">
                        @if($meteran->status == 'aktif')
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                            <i class="fas fa-check-circle mr-1"></i> Aktif
                        </span>
                        @else
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                            <i class="fas fa-times-circle mr-1"></i> Nonaktif
                        </span>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex space-x-2">
                            <a href="{{ route('superuser.meteran.show', $meteran->id) }}" 
                               class="text-green-600 hover:text-green-800" 
                               title="Lihat Detail Meteran">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('superuser.meteran.edit', $meteran->id) }}"
                               class="text-blue-600 hover:text-blue-800"
                               title="Edit Meteran">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('superuser.meteran.delete', $meteran->id) }}"
                               class="text-red-600 hover:text-red-800"
                               title="Hapus Meteran">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-6 px-4 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-tachometer-alt text-4xl text-gray-300 mb-3"></i>
                            <p class="text-lg font-medium mb-2">Belum ada data meteran</p>
                            <p class="text-sm mb-4">Klik tombol "Tambah Meteran" untuk menambahkan meteran baru.</p>
                            <a href="{{ route('superuser.meteran.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
                                <i class="fas fa-plus mr-2"></i> Tambah Meteran Pertama
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