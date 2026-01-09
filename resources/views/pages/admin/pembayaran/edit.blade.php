@extends('layouts.app')

@section('title', 'Edit Pembayaran')

@section('header', 'Edit Pembayaran')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Form Edit Pembayaran</h2>
        <a href="{{ route('admin.pembayaran.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.pembayaran.update', $pembayaran->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Info Tagihan -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Tagihan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pelanggan</label>
                    <p class="text-sm text-gray-900">
                        @if($pembayaran->tagihan && $pembayaran->tagihan->meteran && $pembayaran->tagihan->meteran->user)
                            {{ $pembayaran->tagihan->meteran->user->name ?? $pembayaran->tagihan->meteran->user->nama ?? 'N/A' }}
                        @else
                            Data pelanggan tidak tersedia
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Meteran</label>
                    <p class="text-sm text-gray-900">
                        @if($pembayaran->tagihan && $pembayaran->tagihan->meteran)
                            {{ $pembayaran->tagihan->meteran->nomor_meteran ?? 'N/A' }}
                        @else
                            N/A
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Tagihan</label>
                    <p class="text-sm text-gray-900">
                        @if($pembayaran->tagihan)
                            Rp {{ number_format($pembayaran->tagihan->total_tagihan ?? $pembayaran->tagihan->jumlah ?? 0, 0, ',', '.') }}
                        @else
                            Rp 0
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Tagihan</label>
                    <p class="text-sm text-gray-900">
                        @if($pembayaran->tagihan)
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                @if($pembayaran->tagihan->status == 'sudah_bayar') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $pembayaran->tagihan->status)) }}
                            </span>
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Tanggal Pembayaran -->
        <div>
            <label for="tanggal_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">
                Tanggal Pembayaran <span class="text-red-500">*</span>
            </label>
            <input type="date" 
                   name="tanggal_pembayaran" 
                   id="tanggal_pembayaran" 
                   value="{{ old('tanggal_pembayaran', $pembayaran->tanggal_pembayaran ? \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('Y-m-d') : '') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                   required>
            @error('tanggal_pembayaran')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Jumlah Bayar -->
        <div>
            <label for="jumlah_bayar" class="block text-sm font-medium text-gray-700 mb-2">
                Jumlah Bayar <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                <input type="number" 
                       name="jumlah_bayar" 
                       id="jumlah_bayar" 
                       value="{{ old('jumlah_bayar', $pembayaran->jumlah_bayar ?? 0) }}"
                       step="0.01"
                       min="0"
                       placeholder="0"
                       class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       required>
            </div>
            @error('jumlah_bayar')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Metode Pembayaran -->
        <div>
            <label for="metode_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">
                Metode Pembayaran <span class="text-red-500">*</span>
            </label>
            <select name="metode_pembayaran" id="metode_pembayaran" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                <option value="">-- Pilih Metode Pembayaran --</option>
                <option value="tunai" {{ old('metode_pembayaran', $pembayaran->metode_pembayaran) == 'tunai' ? 'selected' : '' }}>Tunai</option>
                <option value="transfer" {{ old('metode_pembayaran', $pembayaran->metode_pembayaran) == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                <option value="e-wallet" {{ old('metode_pembayaran', $pembayaran->metode_pembayaran) == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
            </select>
            @error('metode_pembayaran')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Status -->
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                Status Pembayaran <span class="text-red-500">*</span>
            </label>
            <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                <option value="">-- Pilih Status --</option>
                <option value="pending" {{ old('status', $pembayaran->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="lunas" {{ old('status', $pembayaran->status) == 'lunas' ? 'selected' : '' }}>Lunas</option>
                <option value="failed" {{ old('status', $pembayaran->status) == 'failed' ? 'selected' : '' }}>Gagal</option>
                <option value="expired" {{ old('status', $pembayaran->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                <option value="cancelled" {{ old('status', $pembayaran->status) == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
            @error('status')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Keterangan -->
        <div>
            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                Keterangan
            </label>
            <textarea name="keterangan" 
                      id="keterangan" 
                      rows="3"
                      placeholder="Tambahkan keterangan pembayaran (opsional)"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('keterangan', $pembayaran->keterangan) }}</textarea>
            @error('keterangan')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Catatan Admin -->
        <div>
            <label for="catatan_admin" class="block text-sm font-medium text-gray-700 mb-2">
                Catatan Admin
            </label>
            <textarea name="catatan_admin" 
                      id="catatan_admin" 
                      rows="3"
                      placeholder="Catatan internal untuk admin (opsional)"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('catatan_admin', $pembayaran->catatan_admin) }}</textarea>
            @error('catatan_admin')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Info Verifikasi -->
        @if($pembayaran->is_verified)
        <div class="bg-blue-50 p-4 rounded-lg">
            <h3 class="text-sm font-medium text-blue-900 mb-2">Status Verifikasi</h3>
            <p class="text-sm text-blue-700">
                <i class="fas fa-shield-check mr-1"></i>
                Pembayaran ini telah diverifikasi oleh admin pada {{ $pembayaran->verified_at ? \Carbon\Carbon::parse($pembayaran->verified_at)->format('d M Y H:i') : 'N/A' }}
            </p>
        </div>
        @endif

        <!-- Buttons -->
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.pembayaran.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                Batal
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-save mr-2"></i> 
                Update Pembayaran
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const jumlahBayarInput = document.getElementById('jumlah_bayar');
    
    // Format angka pada input jumlah bayar
    jumlahBayarInput.addEventListener('input', function() {
        let value = this.value.replace(/[^\d.]/g, '');
        this.value = value;
    });

    // Validate form before submit
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const jumlahBayar = jumlahBayarInput.value;
        const metodePembayaran = document.getElementById('metode_pembayaran').value;
        const status = document.getElementById('status').value;
        
        if (!jumlahBayar || parseFloat(jumlahBayar) <= 0) {
            e.preventDefault();
            alert('Jumlah bayar harus lebih dari 0.');
            jumlahBayarInput.focus();
            return false;
        }
        
        if (!metodePembayaran) {
            e.preventDefault();
            alert('Silakan pilih metode pembayaran.');
            document.getElementById('metode_pembayaran').focus();
            return false;
        }
        
        if (!status) {
            e.preventDefault();
            alert('Silakan pilih status pembayaran.');
            document.getElementById('status').focus();
            return false;
        }
        
        // Confirm before submit
        if (!confirm('Apakah Anda yakin akan mengupdate data pembayaran ini?')) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endpush
@endsection