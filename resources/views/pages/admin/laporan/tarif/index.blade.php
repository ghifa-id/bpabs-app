@extends('layouts.app')

@section('title', 'Laporan Tarif')
@section('header', 'Laporan Tarif')
@section('subtitle', 'Laporan data tarif air sistem BPABS')

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Tarif</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $statistik['total_tarif'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-blue-600 font-medium">
                    <i class="fas fa-list mr-1"></i>
                    Jenis Tarif
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Tarif Terendah</p>
                    <p class="text-3xl font-bold text-green-600">
                        Rp {{ number_format($statistik['tarif_terendah'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-arrow-down text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Per m³</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Tarif Tertinggi</p>
                    <p class="text-3xl font-bold text-red-600">
                        Rp {{ number_format($statistik['tarif_tertinggi'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-arrow-up text-red-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">Per m³</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Rata-rata Tarif</p>
                    <p class="text-3xl font-bold text-purple-600">
                        Rp {{ number_format($statistik['rata_rata_tarif'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-bar text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full">Per m³</span>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('admin.laporan.tarif') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
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
                               placeholder="Cari nama tarif, deskripsi..."
                               class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                    </div>
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
                    <a href="{{ route('admin.laporan.tarif') }}" 
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

    <!-- Tarif Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Tarif</h3>
                <div class="text-sm text-gray-500">
                    Total: {{ $tarif->total() }} tarif
                </div>
            </div>
        </div>

        @if($tarif->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Tarif</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga per m³</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Dibuat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tarif as $index => $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ($tarif->currentPage() - 1) * $tarif->perPage() + $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-full flex items-center justify-center">
                                            <i class="fas fa-money-bill-wave text-white text-sm"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->nama_tarif }}</div>
                                            <div class="text-sm text-gray-500">ID: {{ $item->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-lg font-bold text-green-600">
                                        Rp {{ number_format($item->harga, 0, ',', '.') }}
                                    </div>
                                    <div class="text-xs text-gray-500">per meter kubik</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        @if($item->deskripsi)
                                            {{ Str::limit($item->deskripsi, 60) }}
                                        @else
                                            <span class="text-gray-400 italic">Tidak ada deskripsi</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>{{ $item->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs">{{ $item->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="showDetailTarif({{ $item->id }})" 
                                                class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded-lg transition-colors"
                                                title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('admin.tarif.edit', $item->id) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 p-2 hover:bg-indigo-50 rounded-lg transition-colors"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteTarif({{ $item->id }})" 
                                                class="text-red-600 hover:text-red-900 p-2 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
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
                {{ $tarif->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-money-bill-wave text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada tarif ditemukan</h3>
                <p class="text-gray-500">Tidak ada tarif yang sesuai dengan filter yang dipilih.</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal Detail Tarif -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Detail Tarif</h3>
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

<!-- ✅ FIXED: Modal Confirm Delete -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Konfirmasi Hapus</h3>
            <p class="text-gray-500 text-center mb-6">Apakah Anda yakin ingin menghapus tarif ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex space-x-3">
                <button onclick="closeModal('deleteModal')" 
                        class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    Batal
                </button>
                <button id="confirmDeleteBtn" 
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
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
    let currentTarifId = null;

    // ✅ FIXED: Added missing function showDetailTarif
    function showDetailTarif(id) {
        currentTarifId = id;
        showLoading('Memuat detail tarif...');
        
        fetch(`{{ url('admin/tarif') }}/${id}/detail`, {
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
            if (data.tarif) {
                const tarif = data.tarif;
                
                document.getElementById('detailContent').innerHTML = `
                    <div class="space-y-6">
                        <!-- Tarif Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Informasi Tarif</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nama Tarif</label>
                                        <p class="text-sm text-gray-900 font-medium">${tarif.nama_tarif}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Harga per m³</label>
                                        <p class="text-lg font-bold text-green-600">Rp ${new Intl.NumberFormat('id-ID').format(tarif.harga)}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                        <p class="text-sm text-gray-900">${tarif.deskripsi || 'Tidak ada deskripsi'}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tanggal Dibuat</label>
                                        <p class="text-sm text-gray-900">${new Date(tarif.created_at).toLocaleDateString('id-ID', { 
                                            weekday: 'long', 
                                            year: 'numeric', 
                                            month: 'long', 
                                            day: 'numeric' 
                                        })}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Terakhir Update</label>
                                        <p class="text-sm text-gray-900">${new Date(tarif.updated_at).toLocaleDateString('id-ID', { 
                                            weekday: 'long', 
                                            year: 'numeric', 
                                            month: 'long', 
                                            day: 'numeric' 
                                        })}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tarif Statistics -->
                            <div class="space-y-4">
                                <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Statistik Tarif</h4>
                                <div class="space-y-3">
                                    <div class="bg-blue-50 p-4 rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm text-blue-700 font-medium">Kategori Harga</p>
                                                <p class="text-lg font-bold text-blue-900">
                                                    ${tarif.harga < 5000 ? 'Ekonomis' : 
                                                      tarif.harga < 10000 ? 'Standar' : 'Premium'}
                                                </p>
                                            </div>
                                            <i class="fas fa-tag text-blue-600 text-2xl"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-green-50 p-4 rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm text-green-700 font-medium">Estimasi per 10m³</p>
                                                <p class="text-lg font-bold text-green-900">
                                                    Rp ${new Intl.NumberFormat('id-ID').format(tarif.harga * 10)}
                                                </p>
                                            </div>
                                            <i class="fas fa-calculator text-green-600 text-2xl"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-purple-50 p-4 rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm text-purple-700 font-medium">Estimasi per 20m³</p>
                                                <p class="text-lg font-bold text-purple-900">
                                                    Rp ${new Intl.NumberFormat('id-ID').format(tarif.harga * 20)}
                                                </p>
                                            </div>
                                            <i class="fas fa-chart-line text-purple-600 text-2xl"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Actions -->
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <a href="{{ url('admin/tarif') }}/${tarif.id}/edit" 
                               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                                <i class="fas fa-edit mr-2"></i>
                                Edit Tarif
                            </a>
                            <button onclick="deleteTarif(${tarif.id})" 
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                                <i class="fas fa-trash mr-2"></i>
                                Hapus Tarif
                            </button>
                        </div>
                    </div>
                `;
                
                showModal('detailModal');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showToast('Terjadi kesalahan saat memuat detail tarif', 'error');
        });
    }

    // ✅ FIXED: Improved delete tarif function
    function deleteTarif(id) {
        currentTarifId = id;
        showModal('deleteModal');
        
        document.getElementById('confirmDeleteBtn').onclick = function() {
            showLoading('Menghapus tarif...');
            
            fetch(`{{ url('admin/tarif') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                closeModal('deleteModal');
                
                if (data.success) {
                    showToast(data.message, 'success');
                    location.reload();
                } else {
                    showToast('Gagal menghapus tarif: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                hideLoading();
                closeModal('deleteModal');
                console.error('Error:', error);
                showToast('Terjadi kesalahan saat menghapus tarif', 'error');
            });
        };
    }

    function printPreview() {
        showLoading('Menyiapkan preview cetak...');
        
        const params = new URLSearchParams(window.location.search);
        const printUrl = `{{ route('admin.laporan.tarif.cetak') }}?${params.toString()}`;
        
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

    // Modal functions
    function showModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // ✅ ADDED: Toast notification function
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
        
        // Ctrl+P for print preview
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