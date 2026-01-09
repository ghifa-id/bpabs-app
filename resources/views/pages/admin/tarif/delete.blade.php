@extends('layouts.app')

@section('title', 'Hapus Tarif')

@section('header', 'Hapus Tarif')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Konfirmasi Hapus Tarif</h2>
        <a href="{{ route('admin.tarif.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <!-- Peringatan -->
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-500"></i>
            </div>
            <div class="ml-3">
                <p class="font-medium">Perhatian!</p>
                <p class="text-sm">Anda akan menghapus data tarif berikut. Data yang sudah dihapus tidak dapat dikembalikan.</p>
            </div>
        </div>
    </div>

    <!-- Data Tarif yang akan dihapus -->
    <div class="bg-gray-50 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-info-circle mr-2 text-blue-500"></i>
            Data yang akan dihapus
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">ID Tarif</label>
                    <p class="text-gray-800 font-medium">{{ $tarif->id }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nama Tarif</label>
                    <p class="text-gray-800 font-medium text-lg">{{ $tarif->nama_tarif }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Harga</label>
                    <div class="bg-green-100 text-green-800 px-3 py-2 rounded-lg inline-block">
                        <span class="text-xl font-bold">Rp {{ number_format($tarif->harga, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Deskripsi</label>
                <div class="bg-white p-4 rounded-lg border">
                    @if($tarif->deskripsi)
                        <p class="text-gray-700 leading-relaxed">{{ $tarif->deskripsi }}</p>
                    @else
                        <p class="text-gray-400 italic">Tidak ada deskripsi</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Konfirmasi -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
        <div class="flex items-center mb-4">
            <i class="fas fa-question-circle text-yellow-600 text-2xl mr-3"></i>
            <h3 class="text-lg font-semibold text-yellow-800">Apakah Anda yakin?</h3>
        </div>
        <p class="text-yellow-700 mb-4">
            Tindakan ini akan menghapus tarif <strong>"{{ $tarif->nama_tarif }}"</strong> secara permanen dari database. 
            Pastikan tarif ini tidak sedang digunakan dalam tagihan aktif.
        </p>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-4">
        <a href="{{ route('admin.tarif.show', $tarif->id) }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition duration-300 flex items-center">
            <i class="fas fa-times mr-2"></i> Batal
        </a>
        
        <form action="{{ route('admin.tarif.destroy', $tarif->id) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300 flex items-center"
                    onclick="return confirm('Apakah Anda benar-benar yakin ingin menghapus tarif ini? Data yang dihapus tidak dapat dikembalikan!')">
                <i class="fas fa-trash mr-2"></i> Ya, Hapus Tarif
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto focus pada tombol batal untuk keamanan
    const cancelButton = document.querySelector('a[href*="show"]');
    if (cancelButton) {
        cancelButton.focus();
    }
});
</script>
@endpush
@endsection