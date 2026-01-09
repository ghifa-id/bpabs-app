@extends('layouts.app')

@section('title', 'Input Pembacaan Meteran')
@section('header', 'Input Pembacaan Meteran')
@section('subtitle', 'Input pembacaan meteran pelanggan')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('petugas.meteran.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Meteran
        </a>
    </div>

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

    <form action="{{ route('petugas.meteran.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Pelanggan Field -->
        <div>
            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-user mr-1"></i>
                Pelanggan <span class="text-red-500">*</span>
            </label>
            <select name="user_id" id="user_id" 
                    class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
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

        <!-- Meter Number Field -->
        <div>
            <label for="meteran_display" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-tachometer-alt mr-1"></i>
                Nomor Meteran <span class="text-red-500">*</span>
            </label>
            <input type="text" id="meteran_display" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600"
                   placeholder="Pilih pelanggan terlebih dahulu"
                   readonly>
            <input type="hidden" name="nomor_meteran" id="nomor_meteran" value="{{ old('nomor_meteran') }}">
            @error('nomor_meteran')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">
                Nomor meteran akan otomatis terisi setelah memilih pelanggan
            </p>
        </div>

        <!-- Periode Pembacaan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Bulan -->
            <div>
                <label for="bulan" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    Bulan <span class="text-red-500">*</span>
                </label>
                <select name="bulan" id="bulan" 
                        class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
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
                    Tahun <span class="text-red-500">*</span>
                </label>
                <input type="number" name="tahun" id="tahun" 
                       value="{{ old('tahun', date('Y')) }}" 
                       min="2020" max="2030"
                       class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                       required>
                @error('tahun')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Meter Reading Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Initial Meter -->
            <div>
                <label for="meter_awal" class="block text-sm font-medium text-gray-700 mb-2">
                    Meter Awal (m³) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="number" 
                           id="meter_awal" 
                           name="meter_awal" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                           required min="0" step="0.01">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500">m³</span>
                    </div>
                </div>
                @error('meter_awal')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Final Meter -->
            <div>
                <label for="meter_akhir" class="block text-sm font-medium text-gray-700 mb-2">
                    Meter Akhir (m³) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="number" 
                           id="meter_akhir" 
                           name="meter_akhir" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                           required min="0" step="0.01">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500">m³</span>
                    </div>
                </div>
                @error('meter_akhir')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Usage Preview -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-blue-800 mb-2">
                <i class="fas fa-calculator mr-1"></i> Preview Pemakaian
            </h4>
            <div class="text-sm text-blue-700">
                <p><strong>Pemakaian:</strong> <span id="usage-preview">0</span> m³</p>
                <p><strong>Periode:</strong> <span id="periode-preview">-</span></p>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                <i class="fas fa-save mr-2"></i> Simpan Pembacaan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Data meteran untuk setiap user (akan diisi dari server)
const meteranData = @json(\App\Models\Meteran::with('user')->get()->groupBy('user_id'));

// Calculate and display usage preview
function calculateUsage() {
    const awal = parseFloat(document.getElementById('meter_awal').value) || 0;
    const akhir = parseFloat(document.getElementById('meter_akhir').value) || 0;
    const usage = akhir - awal;
    document.getElementById('usage-preview').textContent = usage >= 0 ? usage.toFixed(2) : '0';
}

// Update periode preview
function updatePeriodePreview() {
    const bulan = document.getElementById('bulan').value || '-';
    const tahun = document.getElementById('tahun').value || '-';
    document.getElementById('periode-preview').textContent = `${bulan} ${tahun}`;
}

// Auto-select meteran berdasarkan user yang dipilih
document.getElementById('user_id').addEventListener('change', function() {
    const userId = this.value;
    const meteranDisplay = document.getElementById('meteran_display');
    const nomorMeteran = document.getElementById('nomor_meteran');
    
    if (userId && meteranData[userId] && meteranData[userId].length > 0) {
        // Ambil meteran pertama (atau meteran aktif) untuk user ini
        const meteran = meteranData[userId][0]; // Jika ada multiple meteran, ambil yang pertama
        
        meteranDisplay.value = meteran.nomor_meteran;
        nomorMeteran.value = meteran.nomor_meteran;
        
        // Jika user hanya punya 1 meteran, tampilkan nomor meteran
        if (meteranData[userId].length === 1) {
            meteranDisplay.value = meteran.nomor_meteran;
        } else {
            // Jika user punya multiple meteran, tampilkan yang aktif atau pertama
            const activeMeteran = meteranData[userId].find(m => m.status === 'aktif') || meteranData[userId][0];
            meteranDisplay.value = activeMeteran.nomor_meteran;
            nomorMeteran.value = activeMeteran.nomor_meteran;
        }
    } else {
        meteranDisplay.value = '';
        nomorMeteran.value = '';
        meteranDisplay.placeholder = userId ? 'Tidak ada meteran untuk pelanggan ini' : 'Pilih pelanggan terlebih dahulu';
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Initialize calculation
    calculateUsage();
    updatePeriodePreview();
    
    // Set event listeners
    document.getElementById('meter_awal').addEventListener('input', calculateUsage);
    document.getElementById('meter_akhir').addEventListener('input', calculateUsage);
    document.getElementById('bulan').addEventListener('change', updatePeriodePreview);
    document.getElementById('tahun').addEventListener('input', updatePeriodePreview);
    
    // If there's old user_id value, trigger change event
    const userId = document.getElementById('user_id').value;
    if (userId) {
        document.getElementById('user_id').dispatchEvent(new Event('change'));
    }
});

// Auto-hide success message after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('[role="alert"]');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s ease-out';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);
</script>
@endpush
@endsection