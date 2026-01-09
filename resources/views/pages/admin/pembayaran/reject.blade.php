@extends('layouts.app')

@section('title', 'Tolak Pembayaran')

@section('header', 'Tolak Pembayaran')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Tolak Pembayaran #{{ $pembayaran->id }}</h2>
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

    <!-- Warning Alert -->
    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded" role="alert">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <p><strong>Peringatan:</strong> Tindakan ini akan menolak pembayaran dan mengubah statusnya menjadi "Gagal". Pastikan Anda memberikan alasan yang jelas.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Informasi Pembayaran -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <i class="fas fa-credit-card mr-2 text-blue-600"></i>
                Informasi Pembayaran
            </h3>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">ID Pembayaran:</span>
                    <span class="text-sm text-gray-900">#{{ $pembayaran->id }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Jumlah Bayar:</span>
                    <span class="text-sm font-semibold text-gray-900">
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
                        @elseif($pembayaran->status == 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i>
                                Pending
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-question-circle mr-1"></i>
                                {{ ucfirst($pembayaran->status ?? 'Tidak diketahui') }}
                            </span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Informasi Pelanggan -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <i class="fas fa-user mr-2 text-green-600"></i>
                Informasi Pelanggan
            </h3>
            
            @if($pembayaran->tagihan && $pembayaran->tagihan->meteran && $pembayaran->tagihan->meteran->user)
                @php
                    $user = $pembayaran->tagihan->meteran->user;
                    $meteran = $pembayaran->tagihan->meteran;
                @endphp
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">Nama:</span>
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
                    
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">Jumlah Tagihan:</span>
                        <span class="text-sm font-semibold text-gray-900">
                            Rp {{ number_format($pembayaran->tagihan->total_tagihan ?? $pembayaran->tagihan->jumlah ?? 0, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500 italic">Informasi pelanggan tidak tersedia</p>
            @endif
        </div>
    </div>

    <!-- Form Penolakan -->
    <div class="bg-red-50 border border-red-200 p-6 rounded-lg">
        <h3 class="text-lg font-medium text-red-900 mb-4 flex items-center">
            <i class="fas fa-times-circle mr-2 text-red-600"></i>
            Form Penolakan Pembayaran
        </h3>
        
        <form action="{{ route('admin.pembayaran.reject', $pembayaran->id) }}" method="POST" id="rejectForm">
            @csrf
            
            <!-- Alasan Penolakan -->
            <div class="mb-6">
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea name="reason" 
                          id="reason" 
                          rows="6"
                          placeholder="Jelaskan alasan mengapa pembayaran ini ditolak. Alasan yang jelas akan membantu pelanggan memahami masalah dan melakukan perbaikan."
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                          required>{{ old('reason') }}</textarea>
                
                @error('reason')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                
                <!-- Character counter -->
                <div class="flex justify-between items-center mt-2">
                    <p class="text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Berikan penjelasan yang jelas dan profesional
                    </p>
                    <span id="charCount" class="text-xs text-gray-500">0/500</span>
                </div>
            </div>

            <!-- Template Alasan (Optional) -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Template Alasan (Opsional)
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <button type="button" 
                            onclick="setReason('Bukti pembayaran tidak valid atau tidak dapat diverifikasi.')"
                            class="text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50 text-sm">
                        <i class="fas fa-file-image mr-2 text-gray-400"></i>
                        Bukti pembayaran tidak valid
                    </button>
                    
                    <button type="button" 
                            onclick="setReason('Jumlah pembayaran tidak sesuai dengan tagihan yang harus dibayar.')"
                            class="text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50 text-sm">
                        <i class="fas fa-calculator mr-2 text-gray-400"></i>
                        Jumlah tidak sesuai
                    </button>
                    
                    <button type="button" 
                            onclick="setReason('Informasi pembayaran tidak lengkap atau tidak jelas.')"
                            class="text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50 text-sm">
                        <i class="fas fa-exclamation-circle mr-2 text-gray-400"></i>
                        Informasi tidak lengkap
                    </button>
                    
                    <button type="button" 
                            onclick="setReason('Pembayaran dilakukan melewati batas waktu yang ditentukan.')"
                            class="text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50 text-sm">
                        <i class="fas fa-clock mr-2 text-gray-400"></i>
                        Melewati batas waktu
                    </button>
                </div>
            </div>

            <!-- Konfirmasi -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           id="confirmReject" 
                           class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50"
                           required>
                    <span class="ml-2 text-sm text-gray-700">
                        Saya yakin ingin menolak pembayaran ini dan telah memberikan alasan yang jelas
                    </span>
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-red-200">
                <a href="{{ route('admin.pembayaran.show', $pembayaran->id) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                    Batal
                </a>
                <button type="submit" 
                        id="submitBtn"
                        class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300 flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                    <i class="fas fa-times mr-2"></i> 
                    Tolak Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const reasonTextarea = document.getElementById('reason');
    const charCount = document.getElementById('charCount');
    const confirmCheckbox = document.getElementById('confirmReject');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('rejectForm');
    
    // Character counter
    reasonTextarea.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length + '/500';
        
        if (length > 500) {
            charCount.classList.add('text-red-500');
            charCount.classList.remove('text-gray-500');
        } else {
            charCount.classList.remove('text-red-500');
            charCount.classList.add('text-gray-500');
        }
        
        checkFormValidity();
    });
    
    // Checkbox validation
    confirmCheckbox.addEventListener('change', function() {
        checkFormValidity();
    });
    
    // Check form validity
    function checkFormValidity() {
        const hasReason = reasonTextarea.value.trim().length > 0 && reasonTextarea.value.length <= 500;
        const isConfirmed = confirmCheckbox.checked;
        
        submitBtn.disabled = !(hasReason && isConfirmed);
    }
    
    // Form submission
    form.addEventListener('submit', function(e) {
        const reason = reasonTextarea.value.trim();
        
        if (!reason) {
            e.preventDefault();
            alert('Alasan penolakan harus diisi.');
            reasonTextarea.focus();
            return false;
        }
        
        if (reason.length > 500) {
            e.preventDefault();
            alert('Alasan penolakan maksimal 500 karakter.');
            reasonTextarea.focus();
            return false;
        }
        
        if (!confirmCheckbox.checked) {
            e.preventDefault();
            alert('Silakan centang konfirmasi terlebih dahulu.');
            confirmCheckbox.focus();
            return false;
        }
        
        // Final confirmation
        if (!confirm('Apakah Anda yakin ingin menolak pembayaran ini?\n\nTindakan ini tidak dapat dibatalkan.')) {
            e.preventDefault();
            return false;
        }
    });
    
    // Set template reason
    window.setReason = function(template) {
        const currentReason = reasonTextarea.value.trim();
        
        if (currentReason && !confirm('Mengganti alasan akan menghapus teks yang sudah ada. Lanjutkan?')) {
            return;
        }
        
        reasonTextarea.value = template;
        reasonTextarea.dispatchEvent(new Event('input'));
        reasonTextarea.focus();
        
        // Move cursor to end
        reasonTextarea.setSelectionRange(template.length, template.length);
    };
    
    // Auto-focus on reason textarea
    reasonTextarea.focus();
});
</script>
@endpush
@endsection