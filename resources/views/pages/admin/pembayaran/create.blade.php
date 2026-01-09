@extends('layouts.app')

@section('title', 'Tambah Pembayaran')

@section('header', 'Tambah Pembayaran')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Form Tambah Pembayaran</h2>
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

    <form action="{{ route('admin.pembayaran.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Pilih Tagihan -->
        <div>
            <label for="tagihan_id" class="block text-sm font-medium text-gray-700 mb-2">
                Pilih Tagihan <span class="text-red-500">*</span>
            </label>
            <select name="tagihan_id" id="tagihan_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                <option value="">-- Pilih Tagihan --</option>

                @if(isset($tagihans) && $tagihans->count() > 0)
                    @foreach($tagihans as $tagihan)
                        @php
                            // Get user and meteran data safely
                            $user = null;
                            $meteran = null;
                            $jumlahTagihan = $tagihan->total_tagihan ?? $tagihan->jumlah ?? 0;
                            
                            if ($tagihan->meteran && $tagihan->meteran->user) {
                                $user = $tagihan->meteran->user;
                                $meteran = $tagihan->meteran;
                            } elseif ($tagihan->pembacaanMeteran && $tagihan->pembacaanMeteran->meteran && $tagihan->pembacaanMeteran->meteran->user) {
                                $user = $tagihan->pembacaanMeteran->meteran->user;
                                $meteran = $tagihan->pembacaanMeteran->meteran;
                            }
                            
                            $userName = $user ? ($user->name ?? $user->nama ?? 'N/A') : 'User tidak ditemukan';
                            $nomorMeteran = $meteran ? $meteran->nomor_meteran : 'N/A';
                            $tanggalTagihan = $tagihan->tanggal_tagihan ? 
                                \Carbon\Carbon::parse($tagihan->tanggal_tagihan)->format('d M Y') : 
                                ($tagihan->created_at ? $tagihan->created_at->format('d M Y') : 'N/A');
                        @endphp
                        
                        <option value="{{ $tagihan->id }}" 
                                data-jumlah="{{ $jumlahTagihan }}"
                                data-pelanggan="{{ $userName }}"
                                data-meteran="{{ $nomorMeteran }}"
                                {{ old('tagihan_id') == $tagihan->id ? 'selected' : '' }}>
                            {{ $userName }} | 
                            Meteran: {{ $nomorMeteran }} | 
                            Tagihan: Rp {{ number_format($jumlahTagihan, 0, ',', '.') }} | 
                            {{ $tanggalTagihan }}
                        </option>
                    @endforeach
                @else
                    <option value="" disabled>Tidak ada tagihan yang belum dibayar</option>
                @endif
            </select>
            @error('tagihan_id')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
            
            @if(!isset($tagihans) || $tagihans->count() == 0)
                <p class="text-sm text-yellow-600 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Tidak ada tagihan yang belum dibayar. Silakan buat tagihan terlebih dahulu.
                </p>
            @endif
        </div>

        <!-- Info Pelanggan dan Meteran (akan terisi otomatis) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pelanggan</label>
                <input type="text" 
                       id="info_pelanggan" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" 
                       readonly 
                       placeholder="Pilih tagihan terlebih dahulu">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Meteran</label>
                <input type="text" 
                       id="info_meteran" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" 
                       readonly 
                       placeholder="Pilih tagihan terlebih dahulu">
            </div>
        </div>

        <!-- Tanggal Bayar -->
        <div>
            <label for="tanggal_bayar" class="block text-sm font-medium text-gray-700 mb-2">
                Tanggal Bayar <span class="text-red-500">*</span>
            </label>
            <input type="date" 
                   name="tanggal_bayar" 
                   id="tanggal_bayar" 
                   value="{{ old('tanggal_bayar', date('Y-m-d')) }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                   required>
            @error('tanggal_bayar')
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
                       value="{{ old('jumlah_bayar') }}"
                       step="0.01"
                       min="0"
                       placeholder="0"
                       class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       required>
            </div>
            <p class="text-xs text-gray-500 mt-1">Jumlah akan terisi otomatis sesuai tagihan yang dipilih</p>
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
                <option value="tunai" {{ old('metode_pembayaran') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                <option value="transfer" {{ old('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                <option value="e-wallet" {{ old('metode_pembayaran') == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
            </select>
            @error('metode_pembayaran')
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
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('keterangan') }}</textarea>
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
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('catatan_admin') }}</textarea>
            @error('catatan_admin')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.pembayaran.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                Batal
            </a>
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300 flex items-center"
                    {{ (!isset($tagihans) || $tagihans->count() == 0) ? 'disabled' : '' }}>
                <i class="fas fa-save mr-2"></i> 
                Simpan Pembayaran
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tagihanSelect = document.getElementById('tagihan_id');
    const jumlahBayarInput = document.getElementById('jumlah_bayar');
    const infoPelanggan = document.getElementById('info_pelanggan');
    const infoMeteran = document.getElementById('info_meteran');
    
    // Auto-fill informasi ketika tagihan dipilih
    tagihanSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            // Ambil data dari atribut data-*
            const jumlah = selectedOption.dataset.jumlah;
            const pelanggan = selectedOption.dataset.pelanggan;
            const meteran = selectedOption.dataset.meteran;
            
            // Isi form dengan data yang sesuai
            jumlahBayarInput.value = parseFloat(jumlah || 0);
            infoPelanggan.value = pelanggan || 'N/A';
            infoMeteran.value = meteran || 'N/A';
        } else {
            // Reset form jika tidak ada yang dipilih
            jumlahBayarInput.value = '';
            infoPelanggan.value = '';
            infoMeteran.value = '';
        }
    });
    
    // Format angka pada input jumlah bayar
    jumlahBayarInput.addEventListener('input', function() {
        let value = this.value.replace(/[^\d.]/g, '');
        this.value = value;
    });

    // Trigger change event jika ada old value (untuk handle validation error)
    if (tagihanSelect.value) {
        tagihanSelect.dispatchEvent(new Event('change'));
    }


    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const tagihanId = tagihanSelect.value;
        const jumlahBayar = jumlahBayarInput.value;
        const metodePembayaran = document.getElementById('metode_pembayaran').value;
        
        if (!tagihanId) {
            e.preventDefault();
            alert('Silakan pilih tagihan terlebih dahulu.');
            tagihanSelect.focus();
            return false;
        }
        
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
        
        // Confirm before submit
        if (!confirm('Apakah Anda yakin data pembayaran sudah benar?')) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endpush
@endsection