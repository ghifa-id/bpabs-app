@extends('layouts.app')

@section('title', 'Laporan Pelanggan')
@section('header', 'Laporan Pelanggan')
@section('subtitle', 'Laporan data pelanggan sistem BPABS')

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Pelanggan</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $statistik['total_users'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 font-medium">{{ $statistik['users_bulan_ini'] ?? 0 }}</span>
                <span class="text-gray-500 ml-1">bulan ini</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Pelanggan Aktif</p>
                    <p class="text-3xl font-bold text-green-600">{{ $statistik['users_active'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-check text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Status Aktif</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Pelanggan Nonaktif</p>
                    <p class="text-3xl font-bold text-red-600">{{ $statistik['users_inactive'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-times text-red-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">Status Nonaktif</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Dengan Meteran</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $statistik['users_dengan_meteran'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-tachometer-alt text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full">Memiliki Meteran</span>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('admin.laporan.pengguna') }}" class="space-y-4">
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
                               placeholder="Cari nama, email, alamat, telepon..."
                               class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                        <option value="">Semua Status</option>
                        <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
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
                    <a href="{{ route('admin.laporan.pengguna') }}" 
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

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Pelanggan</h3>
                <div class="text-sm text-gray-500">
                    Total: {{ $users->total() }} pelanggan
                </div>
            </div>
        </div>

        @if($users->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meteran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $index => $user)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <span class="text-white font-semibold text-sm">
                                                {{ strtoupper(substr($user->name ?? $user->nama ?? 'U', 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name ?? $user->nama ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                            @if($user->trashed())
                                                <span class="text-xs text-red-600 font-medium">(Nonaktif)</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        @if($user->telepon ?? $user->no_hp)
                                            <div class="flex items-center">
                                                <i class="fas fa-phone text-gray-400 mr-2"></i>
                                                {{ $user->telepon ?? $user->no_hp }}
                                            </div>
                                        @endif
                                        @if($user->alamat)
                                            <div class="flex items-center mt-1">
                                                <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                                                <span class="text-xs text-gray-600">{{ Str::limit($user->alamat, 30) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-user mr-1"></i>
                                        Pelanggan
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->trashed())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Nonaktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($user->meteran && $user->meteran->isNotEmpty())
                                        <div class="flex items-center">
                                            <i class="fas fa-tachometer-alt text-green-600 mr-2"></i>
                                            <span class="text-green-600 font-medium">{{ $user->meteran->first()->nomor_meteran }}</span>
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            Status: {{ ucfirst($user->meteran->first()->status ?? 'N/A') }}
                                        </div>
                                    @else
                                        <div class="flex items-center">
                                            <i class="fas fa-minus-circle text-gray-400 mr-2"></i>
                                            <span class="text-gray-400">Belum ada</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>{{ $user->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs">{{ $user->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="showDetailUser({{ $user->id }})" 
                                                class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded-lg transition-colors"
                                                title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($user->trashed())
                                            <button onclick="activateUser({{ $user->id }})" 
                                                    class="text-green-600 hover:text-green-900 p-2 hover:bg-green-50 rounded-lg transition-colors"
                                                    title="Aktifkan">
                                                <i class="fas fa-user-check"></i>
                                            </button>
                                        @else
                                            <button onclick="deactivateUser({{ $user->id }})" 
                                                    class="text-yellow-600 hover:text-yellow-900 p-2 hover:bg-yellow-50 rounded-lg transition-colors"
                                                    title="Nonaktifkan">
                                                <i class="fas fa-user-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada pelanggan ditemukan</h3>
                <p class="text-gray-500">Tidak ada pelanggan yang sesuai dengan filter yang dipilih.</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal Detail User -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Detail Pelanggan</h3>
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
<!-- Alpine.js for dropdown functionality -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
    let currentUserId = null;


    function printPreview() {
        showLoading('Menyiapkan preview cetak...');
        
        // Build current URL with parameters
        const params = new URLSearchParams(window.location.search);
        const printUrl = `{{ route('admin.laporan.pengguna') }}/cetak?${params.toString()}`;
        
        // Open in new window for print preview
        setTimeout(() => {
            window.open(printUrl, '_blank');
            hideLoading();
        }, 1000);
    }


    function showAdvancedOptions() {
        showToast('Fitur opsi lanjutan akan tersedia segera', 'info');
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


    function showDetailUser(id) {
        currentUserId = id;
        showLoading('Memuat detail pelanggan...');
        
        fetch(`{{ url('admin/users') }}/${id}/detail`, {
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
            if (data.user) {
                const user = data.user;
                const stats = data.stats || {};
                
                document.getElementById('detailContent').innerHTML = `
                    <div class="space-y-6">
                        <!-- User Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Informasi Pengguna</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nama</label>
                                        <p class="text-sm text-gray-900">${user.name || user.nama || 'N/A'}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Email</label>
                                        <p class="text-sm text-gray-900">${user.email}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Role</label>
                                        <p class="text-sm text-gray-900">${user.role || 'Pelanggan'}</p>
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
                                        <label class="block text-sm font-medium text-gray-700">Status</label>
                                        <p class="text-sm ${user.deleted_at ? 'text-red-600' : 'text-green-600'}">${user.deleted_at ? 'Nonaktif' : 'Aktif'}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Bergabung</label>
                                        <p class="text-sm text-gray-900">${new Date(user.created_at).toLocaleDateString('id-ID')}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Stats -->
                            <div class="space-y-4">
                                <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Statistik</h4>
                                <div class="space-y-3">
                                    ${stats.meteran_number ? `
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Nomor Meteran</label>
                                            <p class="text-sm text-gray-900">${stats.meteran_number}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Status Meteran</label>
                                            <p class="text-sm text-gray-900">${stats.meteran_status}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Total Tagihan</label>
                                            <p class="text-sm text-gray-900">${stats.total_tagihan || 0}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Tagihan Lunas</label>
                                            <p class="text-sm text-green-600">${stats.tagihan_lunas || 0}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Tagihan Belum Bayar</label>
                                            <p class="text-sm text-red-600">${stats.tagihan_belum_bayar || 0}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Total Pembayaran</label>
                                            <p class="text-sm text-gray-900">Rp ${new Intl.NumberFormat('id-ID').format(stats.pembayaran_sukses || 0)}</p>
                                        </div>
                                    ` : '<p class="text-sm text-gray-500">Belum memiliki meteran</p>'}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            ${user.deleted_at ? `
                                <button onclick="activateUser(${user.id})" 
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                                    <i class="fas fa-user-check mr-2"></i>
                                    Aktifkan Pelanggan
                                </button>
                            ` : `
                                <button onclick="deactivateUser(${user.id})" 
                                        class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                                    <i class="fas fa-user-times mr-2"></i>
                                    Nonaktifkan Pelanggan
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
            showToast('Terjadi kesalahan saat memuat detail pelanggan', 'error');
        });
    }

    // Activate user
    function activateUser(id) {
        if (confirm('Apakah Anda yakin ingin mengaktifkan pelanggan ini?')) {
            showLoading('Mengaktifkan pelanggan...');
            
            fetch(`{{ url('admin/users') }}/${id}/activate`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showToast(data.message, 'success');
                    location.reload();
                } else {
                    showToast('Gagal mengaktifkan pelanggan: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showToast('Terjadi kesalahan saat mengaktifkan pelanggan', 'error');
            });
        }
    }

    // Deactivate user
    function deactivateUser(id) {
        if (confirm('Apakah Anda yakin ingin menonaktifkan pelanggan ini?')) {
            showLoading('Menonaktifkan pelanggan...');
            
            fetch(`{{ url('admin/users') }}/${id}/deactivate`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showToast(data.message, 'success');
                    location.reload();
                } else {
                    showToast('Gagal menonaktifkan pelanggan: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showToast('Terjadi kesalahan saat menonaktifkan pelanggan', 'error');
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