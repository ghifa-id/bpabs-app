@extends('layouts.app')

@section('title', 'Laporan Meteran')
@section('header', 'Laporan Meteran')
@section('subtitle', 'Laporan data meteran air sistem BPABS')

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Meteran</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $statistik['total_meteran'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-tachometer-alt text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 font-medium">{{ $statistik['meteran_bulan_ini'] ?? 0 }}</span>
                <span class="text-gray-500 ml-1">bulan ini</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Meteran Aktif</p>
                    <p class="text-3xl font-bold text-green-600">{{ $statistik['meteran_aktif'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Status Aktif</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Meteran Nonaktif</p>
                    <p class="text-3xl font-bold text-red-600">{{ $statistik['meteran_nonaktif'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">Status Nonaktif</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Persentase Aktif</p>
                    <p class="text-3xl font-bold text-purple-600">
                        {{ $statistik['total_meteran'] > 0 ? round(($statistik['meteran_aktif'] / $statistik['total_meteran']) * 100, 1) : 0 }}%
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-pie text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full">Rasio Aktif</span>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('admin.laporan.meteran') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Cari nomor meteran, alamat, nama pelanggan..."
                               class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ $status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                    <input type="date" 
                           name="tanggal_dari" 
                           value="{{ $tanggal_dari }}"
                           class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                    <input type="date" 
                           name="tanggal_sampai" 
                           value="{{ $tanggal_sampai }}"
                           class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                </div>

                <div class="flex items-end space-x-3">
                    <button type="submit" 
                            class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('admin.laporan.meteran') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2.5 rounded-lg font-medium transition-colors flex items-center">
                        <i class="fas fa-undo mr-2"></i>
                        Reset
                    </a>
                </div>

                <div class="flex items-end justify-end space-x-3">
                    <!-- Print Button -->
                    <button type="button" 
                            onclick="printPreview()"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors flex items-center"
                            title="Preview Cetak">
                        <i class="fas fa-print mr-2"></i>
                        Cetak
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Meteran Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Meteran</h3>
                <div class="text-sm text-gray-500">
                    Total: {{ $meteran->total() }} meteran
                </div>
            </div>
        </div>

        @if($meteran->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Meteran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pasang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($meteran as $index => $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ($meteran->currentPage() - 1) * $meteran->perPage() + $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-blue-600 rounded-full flex items-center justify-center">
                                            <i class="fas fa-tachometer-alt text-white text-sm"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->nomor_meteran }}</div>
                                            <div class="text-sm text-gray-500">ID: {{ $item->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($item->user)
                                        <div class="text-sm text-gray-900">
                                            <div class="font-medium">{{ $item->user->name ?? $item->user->nama ?? 'N/A' }}</div>
                                            <div class="text-gray-500">{{ $item->user->email }}</div>
                                            @if($item->user->telepon ?? $item->user->no_hp)
                                                <div class="flex items-center mt-1">
                                                    <i class="fas fa-phone text-gray-400 mr-1 text-xs"></i>
                                                    <span class="text-xs text-gray-600">{{ $item->user->telepon ?? $item->user->no_hp }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500">
                                            <i class="fas fa-user-slash mr-2"></i>
                                            Belum ada pelanggan
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($item->alamat)
                                        <div class="text-sm text-gray-900">
                                            <div class="flex items-center">
                                                <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                                                {{ Str::limit($item->alamat, 40) }}
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">Lokasi tidak tersedia</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($item->status == 'aktif')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>{{ $item->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs">{{ $item->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.meteran.edit', $item->id) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 p-2 hover:bg-indigo-50 rounded-lg transition-colors"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="showDetailMeteran({{ $item->id }})" 
                                                class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded-lg transition-colors"
                                                title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $meteran->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-tachometer-alt text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada meteran ditemukan</h3>
                <p class="text-gray-500">Tidak ada meteran yang sesuai dengan filter yang dipilih.</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal Detail Meteran -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Detail Meteran</h3>
                <button onclick="closeModal('detailModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="detailContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<div id="loadingModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl p-8 max-w-sm w-full text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto mb-4"></div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Memproses...</h3>
        <p class="text-gray-500" id="loadingText">Sedang menyiapkan laporan</p>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentMeteranId = null;
    
    function showDetailMeteran(id) {
        currentMeteranId = id;
        showLoading('Memuat detail meteran...');
        
        fetch(`{{ url('admin/meteran') }}/${id}/detail`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.meteran) {
                const meteran = data.meteran;
                const user = meteran.user;
                
                document.getElementById('detailContent').innerHTML = `
                    <div class="space-y-6">
                        <!-- Meteran Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Informasi Meteran</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nomor Meteran</label>
                                        <p class="text-sm text-gray-900 font-mono">${meteran.nomor_meteran}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Status</label>
                                        <p class="text-sm ${meteran.status === 'aktif' ? 'text-green-600' : 'text-red-600'}">${meteran.status.charAt(0).toUpperCase() + meteran.status.slice(1)}</p>
                                    </div>
                                    ${meteran.alamat ? `
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Lokasi</label>
                                            <p class="text-sm text-gray-900">${meteran.alamat}</p>
                                        </div>
                                    ` : ''}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tanggal Pasang</label>
                                        <p class="text-sm text-gray-900">${new Date(meteran.created_at).toLocaleDateString('id-ID')}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Terakhir Update</label>
                                        <p class="text-sm text-gray-900">${new Date(meteran.updated_at).toLocaleDateString('id-ID')}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Pelanggan Info -->
                            <div class="space-y-4">
                                <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Informasi Pelanggan</h4>
                                <div class="space-y-3">
                                    ${user ? `
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Nama Pelanggan</label>
                                            <p class="text-sm text-gray-900">${user.name || user.nama || 'N/A'}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Email</label>
                                            <p class="text-sm text-gray-900">${user.email}</p>
                                        </div>
                                        ${user.telepon || user.no_hp ? `
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Telepon</label>
                                                <p class="text-sm text-gray-900">${user.telepon || user.no_hp}</p>
                                            </div>
                                        ` : ''}
                                        ${user.alamat ? `
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Alamat</label>
                                                <p class="text-sm text-gray-900">${user.alamat}</p>
                                            </div>
                                        ` : ''}
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Status Pelanggan</label>
                                            <p class="text-sm ${user.deleted_at ? 'text-red-600' : 'text-green-600'}">${user.deleted_at ? 'Nonaktif' : 'Aktif'}</p>
                                        </div>
                                    ` : `
                                        <div class="text-center py-8">
                                            <i class="fas fa-user-slash text-gray-400 text-3xl mb-2"></i>
                                            <p class="text-gray-500">Meteran belum terhubung dengan pelanggan</p>
                                        </div>
                                    `}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Actions -->
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <a href="{{ url('admin/meteran') }}/${meteran.id}/edit" 
                               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                                <i class="fas fa-edit mr-2"></i>
                                Edit Meteran
                            </a>
                            ${meteran.status === 'aktif' ? `
                                <button onclick="deactivateMeteran(${meteran.id})" 
                                        class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                                    <i class="fas fa-pause-circle mr-2"></i>
                                    Nonaktifkan
                                </button>
                            ` : `
                                <button onclick="activateMeteran(${meteran.id})" 
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                                    <i class="fas fa-power-off mr-2"></i>
                                    Aktifkan
                                </button>
                            `}
                        </div>
                    </div>
                `;
                
                showModal('detailModal');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showToast('Terjadi kesalahan saat memuat detail meteran', 'error');
        });
    }

    // Activate meteran
    function activateMeteran(id) {
        if (confirm('Apakah Anda yakin ingin mengaktifkan meteran ini?')) {
            fetch(`{{ url('admin/meteran') }}/${id}/activate`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    location.reload();
                } else {
                    showToast('Gagal mengaktifkan meteran: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan saat mengaktifkan meteran', 'error');
            });
        }
    }

    // Deactivate meteran
    function deactivateMeteran(id) {
        if (confirm('Apakah Anda yakin ingin menonaktifkan meteran ini?')) {
            fetch(`{{ url('admin/meteran') }}/${id}/deactivate`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    location.reload();
                } else {
                    showToast('Gagal menonaktifkan meteran: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan saat menonaktifkan meteran', 'error');
            });
        }
    }

    // Modal functions
    function showModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function printPreview() {
        showLoading('Menyiapkan preview cetak...');
        
        const params = new URLSearchParams(window.location.search);
        const printUrl = `{{ route('admin.laporan.meteran.cetak') }}?${params.toString()}`;
        
        setTimeout(() => {
            window.open(printUrl, '_blank');
            hideLoading();
        }, 1000);
    }

    function showLoading(message = 'Memproses...') {
        document.getElementById('loadingText').textContent = message;
        document.getElementById('loadingModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function hideLoading() {
        document.getElementById('loadingModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function showToast(message, type = 'info') {
        // Remove existing toasts
        const existingToasts = document.querySelectorAll('.toast-notification');
        existingToasts.forEach(toast => toast.remove());
        
        // Create toast
        const toast = document.createElement('div');
        toast.className = `toast-notification fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 translate-x-full`;
        
        // Set colors based on type
        const colors = {
            success: 'bg-green-50 border border-green-200 text-green-800',
            error: 'bg-red-50 border border-red-200 text-red-800',
            warning: 'bg-yellow-50 border border-yellow-200 text-yellow-800',
            info: 'bg-blue-50 border border-blue-200 text-blue-800'
        };
        
        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };
        
        toast.className += ` ${colors[type] || colors.info}`;
        
        toast.innerHTML = `
            <div class="flex items-center">
                <i class="${icons[type] || icons.info} mr-3"></i>
                <span class="flex-1">${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Trigger animation
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 10);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 300);
        }, 5000);
    }

    // Close modal when clicking outside
    document.querySelectorAll('[id$="Modal"]').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Escape key to close modals
        if (e.key === 'Escape') {
            const openModals = document.querySelectorAll('[id$="Modal"]:not(.hidden)');
            openModals.forEach(modal => {
                closeModal(modal.id);
            });
        }
        

        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            printPreview();
        }
    });

    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('[role="alert"]');
        alerts.forEach(alert => {
            setTimeout(() => {
                if (alert && alert.parentNode) {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.remove();
                        }
                    }, 300);
                }
            }, 5000);
        });
    });
</script>
@endpush