@extends('layouts.app')

@section('title', 'Edit Tagihan')

@section('header', 'Edit Tagihan')

@section('content')
<div class="bg-white rounded-lg shadow-md">
    <!-- Header Section -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Edit Tagihan</h2>
                <p class="text-sm text-gray-600 mt-1">Ubah data tagihan pelanggan</p>
            </div>
            <a href="{{ route('superuser.tagihan.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Form Section -->
    <div class="p-6">
        <form action="{{ route('superuser.tagihan.update', $tagihan) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pelanggan (Read Only) -->
                <div>
                    <label for="user_name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-1"></i>
                        Pelanggan
                    </label>
                    <input type="text" id="user_name" 
                           value="{{ $tagihan->user->name ?? 'N/A' }} - {{ $tagihan->user->email ?? 'N/A' }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600"
                           readonly>
                    <p class="mt-1 text-xs text-gray-500">Pelanggan tidak dapat diubah setelah tagihan dibuat</p>
                </div>

                <!-- Meteran (Read Only) -->
                <div>
                    <label for="meteran_display" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tachometer-alt mr-1"></i>
                        No. Meteran
                    </label>
                    <input type="text" id="meteran_display" 
                           value="{{ $tagihan->meteran->nomor_meteran ?? 'N/A' }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600"
                           readonly>
                    <p class="mt-1 text-xs text-gray-500">Nomor meteran tidak dapat diubah</p>
                </div>

                <!-- Bulan -->
                <div>
                    <label for="bulan" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        Bulan
                    </label>
                    <select name="bulan" id="bulan" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bulan') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Bulan</option>
                        <option value="Januari" {{ old('bulan', $tagihan->bulan) == 'Januari' ? 'selected' : '' }}>Januari</option>
                        <option value="Februari" {{ old('bulan', $tagihan->bulan) == 'Februari' ? 'selected' : '' }}>Februari</option>
                        <option value="Maret" {{ old('bulan', $tagihan->bulan) == 'Maret' ? 'selected' : '' }}>Maret</option>
                        <option value="April" {{ old('bulan', $tagihan->bulan) == 'April' ? 'selected' : '' }}>April</option>
                        <option value="Mei" {{ old('bulan', $tagihan->bulan) == 'Mei' ? 'selected' : '' }}>Mei</option>
                        <option value="Juni" {{ old('bulan', $tagihan->bulan) == 'Juni' ? 'selected' : '' }}>Juni</option>
                        <option value="Juli" {{ old('bulan', $tagihan->bulan) == 'Juli' ? 'selected' : '' }}>Juli</option>
                        <option value="Agustus" {{ old('bulan', $tagihan->bulan) == 'Agustus' ? 'selected' : '' }}>Agustus</option>
                        <option value="September" {{ old('bulan', $tagihan->bulan) == 'September' ? 'selected' : '' }}>September</option>
                        <option value="Oktober" {{ old('bulan', $tagihan->bulan) == 'Oktober' ? 'selected' : '' }}>Oktober</option>
                        <option value="November" {{ old('bulan', $tagihan->bulan) == 'November' ? 'selected' : '' }}>November</option>
                        <option value="Desember" {{ old('bulan', $tagihan->bulan) == 'Desember' ? 'selected' : '' }}>Desember</option>
                    </select>
                    @error('bulan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tahun -->
                <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-1"></i>
                        Tahun
                    </label>
                    <input type="number" name="tahun" id="tahun" 
                           value="{{ old('tahun', $tagihan->tahun) }}" 
                           min="2020" max="2030"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tahun') border-red-500 @enderror"
                           required>
                    @error('tahun')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Meter Awal -->
                <div>
                    <label for="meter_awal" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-play mr-1"></i>
                        Meter Awal (m³)
                    </label>
                    <input type="number" name="meter_awal" id="meter_awal" 
                           value="{{ old('meter_awal', $tagihan->meter_awal) }}" 
                           step="0.01" min="0"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('meter_awal') border-red-500 @enderror"
                           required>
                    @error('meter_awal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Meter Akhir -->
                <div>
                    <label for="meter_akhir" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-stop mr-1"></i>
                        Meter Akhir (m³)
                    </label>
                    <input type="number" name="meter_akhir" id="meter_akhir" 
                           value="{{ old('meter_akhir', $tagihan->meter_akhir) }}" 
                           step="0.01" min="0"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('meter_akhir') border-red-500 @enderror"
                           required>
                    @error('meter_akhir')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tarif per m³ -->
                <div>
                    <label for="tarif_per_m3" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-money-bill-wave mr-1"></i>
                        Tarif per m³ (Rp)
                    </label>
                    <select name="tarif_per_m3" id="tarif_per_m3" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tarif_per_m3') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Tarif</option>
                        @foreach($tarif as $item)
                            <option value="{{ $item->harga }}" {{ old('tarif_per_m3', $tagihan->tarif_per_m3) == $item->harga ? 'selected' : '' }}>
                                {{ $item->kategori }} - Rp {{ number_format($item->harga, 0, ',', '.') }}/m³
                            </option>
                        @endforeach
                    </select>
                    @error('tarif_per_m3')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pemakaian (Auto Calculate) -->
                <div>
                    <label for="pemakaian" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calculator mr-1"></i>
                        Pemakaian (m³)
                    </label>
                    <input type="text" id="pemakaian" 
                           value="{{ number_format($tagihan->pemakaian, 2) }} m³"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100"
                           readonly placeholder="Otomatis dihitung">
                    <p class="mt-1 text-xs text-gray-500">Dihitung otomatis: Meter Akhir - Meter Awal</p>
                </div>

                <!-- Tanggal Jatuh Tempo -->
                <div>
                    <label for="tanggal_jatuh_tempo" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-times mr-1"></i>
                        Tanggal Jatuh Tempo
                    </label>
                    <input type="date" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" 
                           value="{{ old('tanggal_jatuh_tempo', $tagihan->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->format('Y-m-d') : '') }}" 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal_jatuh_tempo') border-red-500 @enderror"
                           required>
                    @error('tanggal_jatuh_tempo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Tagihan -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Status Tagihan
                    </label>
                    <select name="status" id="status" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                        <option value="belum_bayar" {{ old('status', $tagihan->status) == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                        <option value="sudah_bayar" {{ old('status', $tagihan->status) == 'sudah_bayar' ? 'selected' : '' }}>Sudah Bayar</option>
                        <option value="terlambat" {{ old('status', $tagihan->status) == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Current Info Section -->
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <h3 class="text-sm font-medium text-blue-800 mb-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Informasi Tagihan Saat Ini
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-blue-600 font-medium">Nomor Tagihan:</span>
                        <div class="text-blue-800">{{ $tagihan->nomor_tagihan ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <span class="text-blue-600 font-medium">Tanggal Tagihan:</span>
                        <div class="text-blue-800">{{ \Carbon\Carbon::parse($tagihan->tanggal_tagihan)->format('d/m/Y') }}</div>
                    </div>
                    <div>
                        <span class="text-blue-600 font-medium">Total Saat Ini:</span>
                        <div class="text-blue-800 font-semibold">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <span class="text-blue-600 font-medium">Status:</span>
                        <div class="text-blue-800">
                            @if($tagihan->status == 'belum_bayar')
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Belum Bayar</span>
                            @elseif($tagihan->status == 'sudah_bayar')
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Sudah Bayar</span>
                            @elseif($tagihan->status == 'terlambat')
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Terlambat</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Tagihan Preview -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-700">Preview Total Tagihan Baru</h3>
                        <p class="text-xs text-gray-500">Kalkulasi berdasarkan perubahan form</p>
                    </div>
                    <div class="text-right">
                        <div class="text-xl font-bold text-gray-800" id="totalTagihan">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</div>
                        <div class="text-xs text-gray-500" id="detailKalkulasi">{{ number_format($tagihan->pemakaian, 2) }} m³ × Rp {{ number_format($tagihan->tarif_per_m3, 0, ',', '.') }} = Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('superuser.tagihan.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-save mr-2"></i>
                    Update Tagihan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Auto calculate pemakaian dan total tagihan
    function calculateTagihan() {
        const meterAwal = parseFloat(document.getElementById('meter_awal').value) || 0;
        const meterAkhir = parseFloat(document.getElementById('meter_akhir').value) || 0;
        const tarifPerM3 = parseFloat(document.getElementById('tarif_per_m3').value) || 0;
        
        const pemakaian = meterAkhir - meterAwal;
        const totalTagihan = pemakaian * tarifPerM3;
        
        // Update pemakaian field
        document.getElementById('pemakaian').value = pemakaian >= 0 ? pemakaian.toFixed(2) + ' m³' : '0 m³';
        
        // Update total tagihan preview
        if (pemakaian >= 0 && totalTagihan >= 0) {
            document.getElementById('totalTagihan').textContent = 'Rp ' + totalTagihan.toLocaleString('id-ID');
            document.getElementById('detailKalkulasi').textContent = 
                pemakaian.toFixed(2) + ' m³ × Rp ' + tarifPerM3.toLocaleString('id-ID') + ' = Rp ' + totalTagihan.toLocaleString('id-ID');
        } else {
            document.getElementById('totalTagihan').textContent = 'Rp 0';
            document.getElementById('detailKalkulasi').textContent = '0 m³ × Rp 0 = Rp 0';
        }
    }
    
    // Event listeners
    document.getElementById('meter_awal').addEventListener('input', calculateTagihan);
    document.getElementById('meter_akhir').addEventListener('input', calculateTagihan);
    document.getElementById('tarif_per_m3').addEventListener('change', calculateTagihan);
    
    // Validation: meter_akhir harus lebih besar dari meter_awal
    document.getElementById('meter_akhir').addEventListener('blur', function() {
        const meterAwal = parseFloat(document.getElementById('meter_awal').value) || 0;
        const meterAkhir = parseFloat(this.value) || 0;
        
        if (meterAkhir <= meterAwal && meterAkhir > 0) {
            alert('Meter akhir harus lebih besar dari meter awal!');
            this.focus();
        }
    });

    // Auto update status based on due date
    document.getElementById('tanggal_jatuh_tempo').addEventListener('change', function() {
        const today = new Date();
        const dueDate = new Date(this.value);
        const statusSelect = document.getElementById('status');
        
        if (dueDate < today && statusSelect.value === 'belum_bayar') {
            // Jika tanggal jatuh tempo sudah lewat dan status masih belum bayar
            if (confirm('Tanggal jatuh tempo sudah lewat. Apakah ingin mengubah status menjadi "Terlambat"?')) {
                statusSelect.value = 'terlambat';
            }
        }
    });
    
    // Initialize calculation on page load
    document.addEventListener('DOMContentLoaded', function() {
        calculateTagihan();
    });
</script>
@endpush
@endsection