@extends('layouts.app')

@section('title', 'Tambah Tagihan')

@section('header', 'Tambah Tagihan')

@section('content')
<div class="bg-white rounded-lg shadow-md">
    <!-- Header Section -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Tambah Tagihan Baru</h2>
                <p class="text-sm text-gray-600 mt-1">Buat tagihan baru berdasarkan pembacaan meteran</p>
            </div>
            <a href="{{ route('admin.tagihan.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Form Section -->
    <div class="p-6">
        <form action="{{ route('admin.tagihan.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Error Messages -->
            @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                <p class="font-bold">Terjadi kesalahan:</p>
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Success Message -->
            @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                <p>{{ session('success') }}</p>
            </div>
            @endif

            <!-- Pilih Pembacaan Meteran -->
            <div>
                <label for="pembacaan_id" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-tachometer-alt mr-1"></i>
                    Pilih Pembacaan Meteran <span class="text-red-500">*</span>
                </label>
                <select name="pembacaan_id" id="pembacaan_id" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('pembacaan_id') border-red-500 @enderror"
                        required>
                    <option value="">Pilih Pembacaan Meteran</option>
                    @foreach($pembacaans as $p)
                        <option value="{{ $p->id }}" 
                            data-meter-awal="{{ $p->meter_awal }}" 
                            data-meter-akhir="{{ $p->meter_akhir }}" 
                            data-bulan="{{ $p->bulan }}" 
                            data-tahun="{{ $p->tahun }}"
                            {{ old('pembacaan_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->meteran->user->name ?? 'N/A' }} - 
                            {{ $p->meteran->nomor_meteran ?? 'N/A' }} - 
                            {{ $p->bulan }} {{ $p->tahun }} - 
                            Awal: {{ $p->meter_awal }} Akhir: {{ $p->meter_akhir }}
                        </option>
                    @endforeach
                </select>
                @error('pembacaan_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Pembacaan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        Bulan
                    </label>
                    <input type="text" id="info_bulan" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" 
                           readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-1"></i>
                        Tahun
                    </label>
                    <input type="text" id="info_tahun" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" 
                           readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-play mr-1"></i>
                        Meter Awal (m³)
                    </label>
                    <input type="text" id="info_meter_awal" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" 
                           readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-stop mr-1"></i>
                        Meter Akhir (m³)
                    </label>
                    <input type="text" id="info_meter_akhir" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" 
                           readonly>
                </div>
            </div>

            <!-- Tarif per m³ -->
            <div>
                <label for="tarif_per_m3" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-money-bill-wave mr-1"></i>
                    Tarif per m³ (Rp) <span class="text-red-500">*</span>
                </label>
                <select name="tarif_per_m3" id="tarif_per_m3" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tarif_per_m3') border-red-500 @enderror"
                        required>
                    <option value="">Pilih Tarif</option>
                    @foreach($tarif as $item)
                        <option value="{{ $item->harga }}" {{ old('tarif_per_m3') == $item->harga ? 'selected' : '' }}>
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
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100"
                       readonly placeholder="Otomatis dihitung">
                <p class="mt-1 text-xs text-gray-500">Dihitung otomatis: Meter Akhir - Meter Awal</p>
            </div>

            <!-- Tanggal Terbit Tagihan -->
            <div>
                <label for="tanggal_tagihan" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar-plus mr-1"></i>
                    Tanggal Terbit Tagihan <span class="text-red-500">*</span>
                </label>
                <input type="date" name="tanggal_tagihan" id="tanggal_tagihan" 
                       value="{{ old('tanggal_tagihan', date('Y-m-d')) }}" 
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal_tagihan') border-red-500 @enderror"
                       required>
                @error('tanggal_tagihan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal Jatuh Tempo -->
            <div>
                <label for="tanggal_jatuh_tempo" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar-times mr-1"></i>
                    Tanggal Jatuh Tempo <span class="text-red-500">*</span>
                </label>
                <input type="date" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" 
                       value="{{ old('tanggal_jatuh_tempo', date('Y-m-d', strtotime('+30 days'))) }}" 
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal_jatuh_tempo') border-red-500 @enderror"
                       required>
                @error('tanggal_jatuh_tempo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Default: 30 hari dari hari ini</p>
            </div>

            <!-- Biaya Details -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Detail Biaya</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Biaya Pemakaian:</span>
                        <div class="font-semibold text-gray-800" id="biayaPemakaian">Rp 0</div>
                    </div>
                    <div>
                        <span class="text-gray-600">Biaya Admin:</span>
                        <div class="font-semibold text-gray-800">Rp 5.000</div>
                    </div>
                    <div>
                        <span class="text-gray-600">Biaya Beban:</span>
                        <div class="font-semibold text-gray-800">Rp 0</div>
                    </div>
                    <div>
                        <span class="text-gray-600">Denda:</span>
                        <div class="font-semibold text-gray-800">Rp 0</div>
                    </div>
                </div>
            </div>

            <!-- Total Tagihan Preview -->
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-blue-700">Total Tagihan</h3>
                        <p class="text-xs text-blue-600">Termasuk biaya admin dan lainnya</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-blue-800" id="totalTagihan">Rp 0</div>
                        <div class="text-xs text-blue-600" id="detailKalkulasi">0 m³ × Rp 0 + Rp 5.000</div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.tagihan.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Tagihan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Biaya tetap
    const biayaAdmin = 5000;
    const biayaBeban = 0;
    const denda = 0;
    
    // Auto calculate pemakaian dan total tagihan
    function calculateTagihan() {
        const meterAwal = parseFloat(document.getElementById('info_meter_awal').value) || 0;
        const meterAkhir = parseFloat(document.getElementById('info_meter_akhir').value) || 0;
        const tarifPerM3 = parseFloat(document.getElementById('tarif_per_m3').value) || 0;
        
        const pemakaian = Math.max(0, meterAkhir - meterAwal);
        const biayaPemakaian = pemakaian * tarifPerM3;
        const totalTagihan = biayaPemakaian + biayaAdmin + biayaBeban + denda;
        
        // Update pemakaian field
        document.getElementById('pemakaian').value = pemakaian.toFixed(2) + ' m³';
        
        // Update biaya pemakaian
        document.getElementById('biayaPemakaian').textContent = 'Rp ' + biayaPemakaian.toLocaleString('id-ID');
        
        // Update total tagihan preview
        document.getElementById('totalTagihan').textContent = 'Rp ' + totalTagihan.toLocaleString('id-ID');
        document.getElementById('detailKalkulasi').textContent = 
            pemakaian.toFixed(2) + ' m³ × Rp ' + tarifPerM3.toLocaleString('id-ID') + ' + Rp ' + biayaAdmin.toLocaleString('id-ID');
    }
    
    // Update info pembacaan saat dipilih
    document.getElementById('pembacaan_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            document.getElementById('info_bulan').value = selectedOption.dataset.bulan;
            document.getElementById('info_tahun').value = selectedOption.dataset.tahun;
            document.getElementById('info_meter_awal').value = selectedOption.dataset.meterAwal;
            document.getElementById('info_meter_akhir').value = selectedOption.dataset.meterAkhir;
            
            calculateTagihan();
        } else {
            document.getElementById('info_bulan').value = '';
            document.getElementById('info_tahun').value = '';
            document.getElementById('info_meter_awal').value = '';
            document.getElementById('info_meter_akhir').value = '';
        }
    });
    
    // Update tanggal jatuh tempo berdasarkan tanggal tagihan + 30 hari
    function updateJatuhTempo() {
        const tanggalTagihan = document.getElementById('tanggal_tagihan').value;
        if (tanggalTagihan) {
            const tanggal = new Date(tanggalTagihan);
            const jatuhTempo = new Date(tanggal.getTime() + (30 * 24 * 60 * 60 * 1000)); // +30 hari
            const formatDate = jatuhTempo.toISOString().split('T')[0];
            document.getElementById('tanggal_jatuh_tempo').value = formatDate;
        }
    }
    
    // Event listeners
    document.getElementById('tarif_per_m3').addEventListener('change', calculateTagihan);
    document.getElementById('tanggal_tagihan').addEventListener('change', updateJatuhTempo);
    
    // Inisialisasi
    document.addEventListener('DOMContentLoaded', function() {
        // Trigger change event jika ada data old
        if (document.getElementById('pembacaan_id').value) {
            document.getElementById('pembacaan_id').dispatchEvent(new Event('change'));
        }
        
        // Hitung awal
        calculateTagihan();
        updateJatuhTempo();
    });
</script>
@endpush
@endsection