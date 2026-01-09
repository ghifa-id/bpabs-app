<!-- Modal Deactivate User -->
<div id="deactivateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-2">Konfirmasi Deactivate User</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin menonaktifkan pengguna <strong id="userName"></strong>?
                </p>
                <p class="text-sm text-gray-400 mt-2">
                    Pengguna yang dinonaktifkan tidak akan bisa login ke sistem.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="deactivateForm" method="POST" action="">
                    @csrf
                    <div class="flex gap-3 justify-center">
                        <button type="button" 
                            onclick="closeDeactivateModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300 transition-colors duration-200">
                            <i class="fas fa-user-times mr-2"></i>
                            Ya, Nonaktifkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openDeactivateModal(userId, userName) {
    // Set nama user di modal
    document.getElementById('userName').textContent = userName;
    
    // Set action form ke route deactivate
    const form = document.getElementById('deactivateForm');
    form.action = `/admin/users/${userId}/deactivate`;
    
    // Tampilkan modal
    document.getElementById('deactivateModal').classList.remove('hidden');
}

function closeDeactivateModal() {
    document.getElementById('deactivateModal').classList.add('hidden');
}

// Handle form submission
document.getElementById('deactivateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const actionUrl = this.action;
    
    fetch(actionUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Tutup modal
            closeDeactivateModal();
            
            // Tampilkan pesan sukses
            showAlert('success', data.message);
            
            // Reload halaman setelah 1 detik
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showAlert('error', data.message || 'Terjadi kesalahan saat menonaktifkan pengguna');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Terjadi kesalahan saat menonaktifkan pengguna');
    });
});

// Fungsi untuk menampilkan alert
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white`;
    alertDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Hapus alert setelah 3 detik
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// Tutup modal jika user klik di luar modal
document.getElementById('deactivateModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeactivateModal();
    }
});

// Tutup modal dengan tombol ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeactivateModal();
    }
});
</script>