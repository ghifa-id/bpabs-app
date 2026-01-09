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
                <p class="text-sm text-gray-600 mt-1">Buat tagihan baru untuk pelanggan</p>
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
        <form action="{{ route('superuser.tagihan.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pelanggan -->
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-1"></i>
                        Pelanggan
                    </label>
                    <select name="user_id" id="user_id" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('user_id') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Pelanggan</option>
                        @foreach(\App\Models\User::where('role', 'pelanggan')->get() as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} - {{ $user->email }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Meteran (Hidden input, akan diisi otomatis) -->
                <div>
                    <label for="meteran_display" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tachometer-alt mr-1"></i>
                        No. Meteran
                    </label>
                    <input type="text" id="meteran_display" 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600"
                           placeholder="Pilih pelanggan terlebih dahulu"
                           readonly>
                    <input type="hidden" name="meteran_id" id="meteran_id" value="{{ old('meteran_id') }}">
                    @error('meteran_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Nomor meteran akan otomatis terisi setelah memilih pelanggan</p>
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
                        <option value="Januari" {{ old('bulan') == 'Januari' ? 'selected' : '' }}>Januari</option>
                        <option value="Februari" {{ old('bulan') == 'Februari' ? 'selected' : '' }}>Februari</option>
                        <option value="Maret" {{ old('bulan') == 'Maret' ? 'selected' : '' }}>Maret</option>
                        <option value="April" {{ old('bulan') == 'April' ? 'selected' : '' }}>April</option>
                        <option value="Mei" {{ old('bulan') == 'Mei' ? 'selected' : '' }}>Mei</option>
                        <option value="Juni" {{ old('bulan') == 'Juni' ? 'selected' : '' }}>Juni</option>
                        <option value="Juli" {{ old('bulan') == 'Juli' ? 'selected' : '' }}>Juli</option>
                        <option value="Agustus" {{ old('bulan') == 'Agustus' ? 'selected' : '' }}>Agustus</option>
                        <option value="September" {{ old('bulan') == 'September' ? 'selected' : '' }}>September</option>
                        <option value="Oktober" {{ old('bulan') == 'Oktober' ? 'selected' : '' }}>Oktober</option>
                        <option value="November" {{ old('bulan') == 'November' ? 'selected' : '' }}>November</option>
                        <option value="Desember" {{ old('bulan') == 'Desember' ? 'selected' : '' }}>Desember</option>
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
                           value="{{ old('tahun', date('Y')) }}" 
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
                           value="{{ old('meter_awal') }}" 
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
                           value="{{ old('meter_akhir') }}" 
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
                        Tanggal Terbit Tagihan
                    </label>
                    <input type="date" name="tanggal_tagihan" id="tanggal_tagihan" 
                           value="{{ old('tanggal_tagihan', date('Y-m-d')) }}" 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal_tagihan') border-red-500 @enderror">
                </div>

                <!-- Tanggal Jatuh Tempo -->
                <div>
                    <label for="tanggal_jatuh_tempo" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-times mr-1"></i>
                        Tanggal Jatuh Tempo
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
                <a href="{{ route('superuser.tagihan.index') }}" 
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
    // Data meteran untuk setiap user (akan diisi dari server)
    const meteranData = @json(\App\Models\Meteran::with('user')->get()->groupBy('user_id'));
    
    // Biaya tetap
    const biayaAdmin = 5000;
    const biayaBeban = 0;
    const denda = 0;
    
    // Auto calculate pemakaian dan total tagihan
    function calculateTagihan() {
        const meterAwal = parseFloat(document.getElementById('meter_awal').value) || 0;
        const meterAkhir = parseFloat(document.getElementById('meter_akhir').value) || 0;
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
    
    // Update tanggal jatuh tempo berdasarkan tanggal tagihan + 30 hari
    function updateJatuhTempo() {
        const tanggalTagihan = document.getElementById('tanggal_tagihan').value;
        if (tanggalTagihan) {
            const tanggalTagihan = new Date(tanggalTagihan);
            const jatuhTempo = new Date(tanggalTagihan.getTime() + (30 * 24 * 60 * 60 * 1000)); // +30 hari
            const formatDate = jatuhTempo.toISOString().split('T')[0];
            document.getElementById('tanggal_jatuh_tempo').value = formatDate;
        } else {
            // Jika tanggal tagihan kosong, set default
            const today = new Date();
            const jatuhTempo = new Date(today.getTime() + (30 * 24 * 60 * 60 * 1000)); // +30 hari
            const formatDate = jatuhTempo.toISOString().split('T')[0];
            
            if (!document.getElementById('tanggal_jatuh_tempo').value) {
                document.getElementById('tanggal_jatuh_tempo').value = formatDate;
            }
        }
    }
    
    // Event listeners
    document.getElementById('meter_awal').addEventListener('input', calculateTagihan);
    document.getElementById('meter_akhir').addEventListener('input', calculateTagihan);
    document.getElementById('tarif_per_m3').addEventListener('change', calculateTagihan);
    document.getElementById('tanggal_tagihan').addEventListener('change', updateJatuhTempo);
    
    // Validation: meter_akhir harus lebih besar dari meter_awal
    document.getElementById('meter_akhir').addEventListener('blur', function() {
        const meterAwal = parseFloat(document.getElementById('meter_awal').value) || 0;
        const meterAkhir = parseFloat(this.value) || 0;
        
        if (meterAkhir <= meterAwal && meterAkhir > 0) {
            alert('Meter akhir harus lebih besar dari meter awal!');
            this.focus();
        }
    });
    
    // Auto-select meteran berdasarkan user yang dipilih
    document.getElementById('user_id').addEventListener('change', function() {
        const userId = this.value;
        const meteranDisplay = document.getElementById('meteran_display');
        const meteranId = document.getElementById('meteran_id');
        
        if (userId && meteranData[userId] && meteranData[userId].length > 0) {
            // Ambil meteran pertama (atau meteran aktif) untuk user ini
            const meteran = meteranData[userId][0]; // Jika ada multiple meteran, ambil yang pertama
            
            meteranDisplay.value = meteran.nomor_meteran;
            meteranId.value = meteran.id;
            
            // Jika user hanya punya 1 meteran, tampilkan nomor meteran
            if (meteranData[userId].length === 1) {
                meteranDisplay.value = meteran.nomor_meteran;
            } else {
                // Jika user punya multiple meteran, tampilkan yang aktif atau pertama
                const aktiveMeteran = meteranData[userId].find(m => m.status === 'aktif') || meteranData[userId][0];
                meteranDisplay.value = aktiveMeteran.nomor_meteran;
                meteranId.value = aktiveMeteran.id;
            }
        } else {
            meteranDisplay.value = '';
            meteranId.value = '';
            meteranDisplay.placeholder = userId ? 'Tidak ada meteran untuk pelanggan ini' : 'Pilih pelanggan terlebih dahulu';
        }
        
        // Reset kalkulasi
        calculateTagihan();
    });
    
    // Inisialisasi jika ada old value
    document.addEventListener('DOMContentLoaded', function() {
        updateJatuhTempo();
        
        const userId = document.getElementById('user_id').value;
        if (userId) {
            // Trigger change event untuk memuat meteran
            document.getElementById('user_id').dispatchEvent(new Event('change'));
        }
        
        // Initial calculation
        calculateTagihan();
    });
</script>
@endpush
@endsection