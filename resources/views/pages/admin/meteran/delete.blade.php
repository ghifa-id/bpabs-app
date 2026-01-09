@extends('layouts.app')

@section('title', 'Hapus Meteran')

@section('header', 'Hapus Meteran')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Konfirmasi Penghapusan Meteran</h2>
    </div>

    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">
                    <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan. Data meteran akan dihapus secara permanen.
                </p>
            </div>
        </div>
    </div>

    <div class="bg-gray-50 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Meteran yang akan dihapus:</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="border-b border-gray-200 pb-2">
                <dt class="text-sm font-medium text-gray-500">Nomor Meteran</dt>
                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $meteran->nomor_meteran }}</dd>
            </div>
            
            <div class="border-b border-gray-200 pb-2">
                <dt class="text-sm font-medium text-gray-500">Nama Pelanggan</dt>
                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $meteran->user->name }}</dd>
            </div>
            
            <div class="border-b border-gray-200 pb-2">
                <dt class="text-sm font-medium text-gray-500">Email Pelanggan</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $meteran->user->email }}</dd>
            </div>
            
            <div class="border-b border-gray-200 pb-2">
                <dt class="text-sm font-medium text-gray-500">Status Meteran</dt>
                <dd class="mt-1">
                    @if($meteran->status == 'aktif')
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Aktif</span>
                    @else
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Nonaktif</span>
                    @endif
                </dd>
            </div>
        </div>
    </div>

    <!-- Notifikasi Konfirmasi -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
        <div class="flex items-center justify-center">
            <div class="flex-shrink-0">
                <i class="fas fa-question-circle text-yellow-500 text-2xl"></i>
            </div>
            <div class="ml-4 text-center">
                <h4 class="text-lg font-medium text-yellow-800">
                    Apakah Anda yakin ingin menghapus meteran ini?
                </h4>
                <p class="mt-2 text-sm text-yellow-700">
                    Meteran dengan nomor <strong>"{{ $meteran->nomor_meteran }}"</strong> 
                    milik pelanggan <strong>"{{ $meteran->user->name }}"</strong> akan dihapus secara permanen.
                </p>
            </div>
        </div>
    </div>

    <div class="flex justify-center space-x-4">
        <a href="{{ route('superuser.meteran.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-8 rounded-lg transition duration-300 flex items-center shadow-md">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar
        </a>
        
        <a href="#" 
           id="confirmDelete"
           data-meteran-id="{{ $meteran->id }}"
           data-meteran-nomor="{{ $meteran->nomor_meteran }}"
           data-pelanggan-nama="{{ $meteran->user->name }}"
           class="bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-8 rounded-lg transition duration-300 flex items-center shadow-md">
            <i class="fas fa-trash mr-2"></i>
            Ya, Hapus Meteran
        </a>
    </div>
</div>

<!-- Modal Konfirmasi Final -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 m-4 max-w-md w-full">
        <div class="flex items-center justify-center mb-4">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
        </div>
        <div class="text-center">
            <h3 class="text-lg font-medium text-gray-900 mb-2">
                Konfirmasi Terakhir
            </h3>
            <p class="text-sm text-gray-500 mb-6">
                Apakah Anda benar-benar yakin ingin menghapus meteran ini? 
                Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>
        <div class="flex justify-center space-x-3">
            <button type="button" 
                    id="cancelDelete"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition duration-300">
                Batal
            </button>
            <form id="deleteForm" action="" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                    Hapus Sekarang
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    const deleteModal = document.getElementById('deleteModal');
    const cancelDeleteBtn = document.getElementById('cancelDelete');
    const deleteForm = document.getElementById('deleteForm');
    const backButton = document.querySelector('a[href*="meteran.index"]');
    
    // Auto focus pada tombol kembali untuk keamanan
    if (backButton) {
        backButton.focus();
    }

    // Event listener untuk tombol konfirmasi delete
    confirmDeleteBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        const meteranId = this.getAttribute('data-meteran-id');
        const deleteUrl = `{{ route('superuser.meteran.destroy', ':id') }}`.replace(':id', meteranId);
        
        // Set action form
        deleteForm.setAttribute('action', deleteUrl);
        
        // Tampilkan modal
        deleteModal.classList.remove('hidden');
        deleteModal.classList.add('flex');
    });

    // Event listener untuk tombol batal di modal
    cancelDeleteBtn.addEventListener('click', function() {
        deleteModal.classList.add('hidden');
        deleteModal.classList.remove('flex');
    });

    // Event listener untuk click outside modal
    deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
            deleteModal.classList.add('hidden');
            deleteModal.classList.remove('flex');
        }
    });

    // Event listener untuk ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            deleteModal.classList.add('hidden');
            deleteModal.classList.remove('flex');
        }
    });
});
</script>
@endsection