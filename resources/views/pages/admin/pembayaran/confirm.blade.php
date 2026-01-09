@extends('layouts.app')

@section('title', 'Konfirmasi Pembayaran')

@section('header', 'Konfirmasi Pembayaran')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Konfirmasi Pembayaran #{{ $pembayaran->id }}</h2>
        <a href="{{ route('admin.pembayaran.show', $pembayaran->id) }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Info Alert -->
    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded" role="alert">
        <div class="flex items-center">
            <i class="fas fa-info-circle mr-2"></i>
            <p><strong>Informasi:</strong> Setelah dikonfirmasi, pembayaran ini akan diverifikasi dan tidak dapat diubah lagi.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Informasi Pembayaran -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <i class="fas fa-credit-card mr-2 text-blue-600"></i>
                Detail Pembayaran
            </h3>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">ID Pembayaran:</span>
                    <span class="text-sm text-gray-900">#{{ $pembayaran->id }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Jumlah Bayar:</span>
                    <span class="text-sm font-semibold text-green-600">
                        Rp {{ number_format($pembayaran->jumlah ?? $pembayaran->jumlah_bayar ?? 0, 0, ',', '.') }}
                    </span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Tanggal Pembayaran:</span>
                    <span class="text-sm text-gray-900">
                        @if($pembayaran->tanggal_pembayaran)
                            {{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d F Y, H:i') }}
                        @else
                            <span class="text-gray-400">Belum diatur</span>
                        @endif
                    </span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Metode Pembayaran:</span>
                    <span class="text-sm text-gray-900">
                        @if($pembayaran->metode_pembayaran)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($pembayaran->metode_pembayaran == 'tunai') bg-green-100 text-green-800
                                @elseif($pembayaran->metode_pembayaran == 'transfer') bg-blue-100 text-blue-800
                                @else bg-purple-100 text-purple-800 @endif">
                                {{ ucfirst(str_replace('-', ' ', $pembayaran->metode_pembayaran)) }}
                            </span>
                        @else
                            <span class="text-gray-400">Tidak diketahui</span>
                        @endif
                    </span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Status Saat Ini:</span>
                    <span class="text-sm">
                        @if($pembayaran->status == 'lunas')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Lunas
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i>
                                {{ ucfirst($pembayaran->status ?? 'Pending') }}
                            </span>
                        @endif
                    </span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Status Verifikasi:</span>
                    <span class="text-sm">
                        @if($pembayaran->is_verified)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-shield-check mr-1"></i>
                                Sudah Terverifikasi
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Belum Verifikasi
                            </span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Informasi Pelanggan dan Tagihan -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <i class="fas fa-user mr-2 text-green-600"></i>
                Informasi Pelanggan & Tagihan
            </h3>
            
            @if($pembayaran->tagihan)
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">ID Tagihan:</span>
                        <span class="text-sm text-gray-900">#{{ $pembayaran->tagihan->id }}</span>
                    </div>
                    
                    @if($pembayaran->tagihan->meteran && $pembayaran->tagihan->meteran->user)
                        @php
                            $user = $pembayaran->tagihan->meteran->user;
                            $meteran = $pembayaran->tagihan->meteran;
                        @endphp
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700">Nama Pelanggan:</span>
                            <span class="text-sm text-gray-900">{{ $user->name ?? $user->nama ?? 'N/A' }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700">Email:</span>
                            <span class="text-sm text-gray-900">{{ $user->email ?? 'N/A' }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700">No. Meteran:</span>
                            <span class="text-sm text-gray-900">{{ $meteran->nomor_meteran ?? 'N/A' }}</span>
                        </div>
                        
                        @if($meteran->alamat)
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-700">Alamat:</span>
                                <span class="text-sm text-gray-900">{{ $meteran->alamat }}</span>
                            </div>
                        @endif
                    @endif
                    
                    <div class="border-t pt-3 mt-3">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700">Jumlah Tagihan:</span>
                            <span class="text-sm font-semibold text-gray-900">
                                Rp {{ number_format($pembayaran->tagihan->total_tagihan ?? $pembayaran->tagihan->jumlah ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700">Status Tagihan:</span>
                            <span class="text-sm">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    @if($pembayaran->tagihan->status == 'sudah_bayar') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $pembayaran->tagihan->status ?? 'N/A')) }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500 italic">Informasi tagihan tidak tersedia</p>
            @endif
        </div>
    </div>

    <!-- Keterangan (jika ada) -->
    @if($pembayaran->keterangan)
        <div class="mb-6 bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                <i class="fas fa-sticky-note mr-2 text-yellow-600"></i>
                Keterangan Pembayaran
            </h3>
            <p class="text-sm text-gray-900 bg-white p-3 rounded border">{{ $pembayaran->keterangan }}</p>
        </div>
    @endif

    <!-- Validation Checklist -->
    <div class="mb-6 bg-yellow-50 border border-yellow-200 p-6 rounded-lg">
        <h3 class="text-lg font-medium text-yellow-900 mb-4 flex items-center">
            <i class="fas fa-clipboard-check mr-2 text-yellow-600"></i>
            Checklist Verifikasi
        </h3>
        
        <div class="space-y-3">
            <label class="flex items-center">
                <input type="checkbox" id="check1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-700">Jumlah pembayaran sesuai dengan tagihan</span>
            </label>
            
            <label class="flex items-center">
                <input type="checkbox" id="check2" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-700">Metode pembayaran valid dan sesuai</span>
            </label>
            
            <label class="flex items-center">
                <input type="checkbox" id="check3" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-700">Data pelanggan dan meteran benar</span>
            </label>
            
            <label class="flex items-center">
                <input type="checkbox" id="check4" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-700">Tidak ada indikasi kecurangan atau kesalahan</span>
            </label>
        </div>
    </div>

    <!-- Confirmation Section -->
    <div class="bg-green-50 border border-green-200 p-6 rounded-lg">
        <h3 class="text-lg font-medium text-green-900 mb-4 flex items-center">
            <i class="fas fa-check-circle mr-2 text-green-600"></i>
            Konfirmasi Verifikasi
        </h3>
        
        @if($pembayaran->is_verified)
            <!-- Already Verified -->
            <div class="text-center py-4">
                <div class="bg-blue-100 rounded-full p-4 w-16 h-16 mx-auto mb-4">
                    <i class="fas fa-shield-check text-blue-600 text-2xl"></i>
                </div>
                <h4 class="text-lg font-medium text-blue-900 mb-2">Pembayaran Sudah Terverifikasi</h4>
                <p class="text-blue-700 mb-4">
                    Pembayaran ini telah diverifikasi pada 
                    {{ $pembayaran->verified_at ? \Carbon\Carbon::parse($pembayaran->verified_at)->format('d F Y, H:i') : 'waktu tidak diketahui' }}
                </p>
                <a href="{{ route('admin.pembayaran.show', $pembayaran->id) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-300 inline-flex items-center">
                    <i class="fas fa-eye mr-2"></i>
                    Lihat Detail
                </a>
            </div>
        @else
            <!-- Confirmation Form -->
            <form action="{{ route('admin.pembayaran.confirm', $pembayaran->id) }}" method="POST" id="confirmForm">
                @csrf
                
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               id="finalConfirm" 
                               class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                               required>
                        <span class="ml-2 text-sm text-gray-700">
                            <strong>Saya konfirmasi bahwa:</strong> Semua data telah diperiksa dan pembayaran ini valid untuk diverifikasi
                        </span>
                    </label>
                </div>

                <div class="mb-6">
                    <label for="admin_note" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan Admin (Opsional)
                    </label>
                    <textarea name="admin_note" 
                              id="admin_note" 
                              rows="3"
                              placeholder="Tambahkan catatan mengenai verifikasi ini (opsional)"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.pembayaran.show', $pembayaran->id) }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                        Batal
                    </a>
                    <button type="submit" 
                            id="submitBtn"
                            class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300 flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                        <i class="fas fa-check mr-2"></i> 
                        Konfirmasi & Verifikasi
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('#check1, #check2, #check3, #check4');
    const finalConfirm = document.getElementById('finalConfirm');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('confirmForm');
    
    // Check if all validation checkboxes are checked
    function updateFinalConfirmState() {
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        finalConfirm.disabled = !allChecked;
        
        if (!allChecked) {
            finalConfirm.checked = false;
        }
        
        updateSubmitButton();
    }
    
    // Update submit button state
    function updateSubmitButton() {
        if (submitBtn) {
            const allValidationChecked = Array.from(checkboxes).every(cb => cb.checked);
            const finalConfirmed = finalConfirm.checked;
            
            submitBtn.disabled = !(allValidationChecked && finalConfirmed);
        }
    }
    
    // Add event listeners to validation checkboxes
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateFinalConfirmState);
    });
    
    // Add event listener to final confirmation
    if (finalConfirm) {
        finalConfirm.addEventListener('change', updateSubmitButton);
    }
    
    // Form submission validation
    if (form) {
        form.addEventListener('submit', function(e) {
            const allValidationChecked = Array.from(checkboxes).every(cb => cb.checked);
            const finalConfirmed = finalConfirm.checked;
            
            if (!allValidationChecked) {
                e.preventDefault();
                alert('Harap lengkapi semua checklist verifikasi terlebih dahulu.');
                return false;
            }
            
            if (!finalConfirmed) {
                e.preventDefault();
                alert('Harap centang konfirmasi final untuk melanjutkan.');
                return false;
            }
            
            // Final confirmation dialog
            if (!confirm('Apakah Anda yakin ingin memverifikasi pembayaran ini?\n\nSetelah diverifikasi, pembayaran tidak dapat diubah lagi.')) {
                e.preventDefault();
                return false;
            }
        });
    }
    
    // Initialize button state
    updateSubmitButton();
});
</script>
@endpush
@endsection