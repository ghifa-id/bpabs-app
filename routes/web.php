<?php

use App\Http\Controllers\Pelanggan\TagihanController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\RegisterController;

// Controlllers superuser
use App\Http\Controllers\Superuser\DashboardController as SuperuserDashboardController;
use App\Http\Controllers\Superuser\UserController as SuperuserUserController;
use App\Http\Controllers\Superuser\MeteranController as SuperuserMeteranController;
use App\Http\Controllers\Superuser\PembayaranController as SuperuserPembayaranController;
use App\Http\Controllers\Superuser\TarifController as SuperuserTarifController;
use App\Http\Controllers\Superuser\TagihanController as SuperuserTagihanController;

// Controllers pelanggan
use App\Http\Controllers\Pelanggan\DashboardController as PelangganDashboardController;
use App\Http\Controllers\Pelanggan\TagihanController as PelangganTagihanController;
use App\Http\Controllers\Pelanggan\RiwayatController as PelangganRiwayatController;
use App\Http\Controllers\Pelanggan\BayarController as PelangganBayarController;
use App\Http\Controllers\Pelanggan\ProfilController as PelangganProfilController;

// Controllers admin
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\MeteranController as AdminMeteranController;
use App\Http\Controllers\Admin\PembayaranController as AdminPembayaranController;
use App\Http\Controllers\Admin\TagihanController as AdminTagihanController;
use App\Http\Controllers\Admin\TarifController as AdminTarifController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;

// Controllers petugas
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboardController;
use App\Http\Controllers\Petugas\MeteranController as PetugasMeteranController;
use App\Http\Controllers\Petugas\PembacaanController as PetugasPembacaanController;

// Debug controller
use App\Http\Controllers\Debug\MidtransDebugController;

// Route utama dengan redirect logic dan welcome page
Route::get('/', function(){
    if (Auth::check()){
        $user = Auth::user();
        return match($user->role) {
            'pelanggan' => redirect()->route('pelanggan.dashboard.index'),
            'superuser' => redirect()->route('superuser.dashboard.index'),
            'admin' => redirect()->route('admin.dashboard.index'),
            'petugas' => redirect()->route('petugas.dashboard.index'),
            default => redirect()->route('login')
        };
    } else {
        // Tampilkan welcome page untuk user yang belum login
        return view('welcome');
    }
})->name('home');

// Route khusus untuk welcome page (opsional)
Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

// Auth routes untuk guest
Route::middleware('guest')->group(function () {
    // Route untuk tampil form login (GET)
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    
    // Route untuk proses login (POST) 
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    
    // Route untuk register
    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::get('/auth-check', function () {
    if (Auth::check()) {
        return response()->json([
            'message' => 'User is authenticated',
            'user' => Auth::user(),
        ]);
    }
    return response()->json(['message' => 'User is not Authenticated']);
});

// ========================================
// MIDTRANS CALLBACK ROUTES - HARUS DI LUAR MIDDLEWARE AUTH
// ========================================

// 1. Webhook callback dari Midtrans server (POST only) - TANPA AUTH
Route::post('/pelanggan/bayar/webhook', [PelangganBayarController::class, 'handleCallback'])
    ->name('pelanggan.bayar.webhook');

// 2. Frontend callback untuk redirect user (GET/POST) - DENGAN AUTH
Route::match(['GET', 'POST'], '/pelanggan/bayar/callback', [PelangganBayarController::class, 'paymentCallback'])
    ->middleware('auth')
    ->name('pelanggan.bayar.callback');
// 3. Alternative callback route - DENGAN AUTH
Route::match(['GET', 'POST'], '/pelanggan/bayar/payment-callback', [PelangganBayarController::class, 'paymentCallback'])
    ->middleware('auth')
    ->name('pelanggan.bayar.payment-callback');

// ========================================
// ROUTE ADMIN
// ========================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'UserAccess:admin'])->group(function() {
    // Dashboard
    Route::prefix('dashboard')->name('dashboard.')->controller(AdminDashboardController::class)->group(function() {
        Route::get('/', 'index')->name('index');
    });

    Route::get('pembayaran/{id}/confirm', [AdminPembayaranController::class, 'showConfirmForm'])
        ->name('pembayaran.show.confirm');

    Route::get('pembayaran/{id}/reject', [AdminPembayaranController::class, 'showRejectForm'])
        ->name('pembayaran.show.reject');

    Route::post('pembayaran/{id}/confirm', [AdminPembayaranController::class, 'confirmPayment'])
        ->name('pembayaran.confirm');

    Route::post('pembayaran/{id}/reject', [AdminPembayaranController::class, 'rejectPayment'])
        ->name('pembayaran.reject');
    

    Route::get('pembayaran/search/tagihan', [AdminPembayaranController::class, 'searchTagihan'])
        ->name('pembayaran.search-tagihan');
    
    Route::get('pembayaran/statistics', [AdminPembayaranController::class, 'getPaymentStatistics'])
        ->name('pembayaran.statistics');
    
    Route::post('pembayaran/generate-report', [AdminPembayaranController::class, 'generateReport'])
        ->name('pembayaran.generate-report');
    
    Route::get('pembayaran/export', [AdminPembayaranController::class, 'export'])
        ->name('pembayaran.export');
    

    Route::post('tagihan/{id}/reset-manual-status', [AdminTagihanController::class, 'resetManualStatus'])
         ->name('tagihan.reset-manual-status');
    

    Route::get('tagihan/{id}/check-status-consistency', [AdminPembayaranController::class, 'checkStatusConsistency'])
         ->name('tagihan.check-status-consistency');
         
    // Alternative route via pembayaran (if pembayaran exists)
    Route::get('pembayaran/{id}/check-status-consistency', [AdminPembayaranController::class, 'checkStatusConsistency'])
         ->name('pembayaran.check-status-consistency');
    

    Route::post('tagihan/{id}/force-sync-status', [AdminPembayaranController::class, 'forceSyncStatus'])
         ->name('tagihan.force-sync-status');
         
    Route::post('pembayaran/{id}/force-sync-status', [AdminPembayaranController::class, 'forceSyncStatus'])
         ->name('pembayaran.force-sync-status');
    
    // Resource routes untuk semua module
    Route::resource('users', AdminUserController::class);
    
    Route::resource('meteran', AdminMeteranController::class);
    Route::get('meteran/{meteran}/delete', [AdminMeteranController::class, 'delete'])->name('meteran.delete');
    
    Route::resource('pembayaran', AdminPembayaranController::class);
    Route::get('pembayaran/{id}/delete', [AdminPembayaranController::class, 'delete'])->name('pembayaran.delete');
    Route::delete('pembayaran/{pembayaran}', [AdminPembayaranController::class, 'destroy'])->name('pembayaran.destroy');
    
    Route::resource('tagihan', AdminTagihanController::class);
    Route::get('tagihan/{tagihan}/delete', [AdminTagihanController::class, 'delete'])->name('tagihan.delete');
    
    Route::resource('tarif', AdminTarifController::class);
    Route::get('tarif/{tarif}/delete', [AdminTarifController::class, 'delete'])->name('tarif.delete');

    // User management routes (activate, deactivate, force delete)
    Route::post('users/{id}/activate', [AdminUserController::class, 'activate'])->name('users.activate');
    Route::post('users/{id}/deactivate', [AdminUserController::class, 'deactivate'])->name('users.deactivate');
    Route::delete('users/{id}/force-delete', [AdminUserController::class, 'forceDelete'])->name('users.force-delete');

    // LAPORAN ROUTES
    Route::prefix('laporan')->name('laporan.')->group(function() {
        Route::get('/pengguna', [AdminLaporanController::class, 'index'])->name('pengguna');
        Route::get('/pengguna/cetak', [AdminLaporanController::class, 'cetak'])->name('pengguna.cetak');
        Route::get('/pengguna/{user}', [AdminLaporanController::class, 'show'])->name('pengguna.show');
        Route::get('/meteran', [AdminLaporanController::class, 'meteran'])->name('meteran');
        Route::get('meteran/cetak', [AdminLaporanController::class, 'cetakMeteran'])->name('meteran.cetak');
        Route::get('/tarif', [AdminLaporanController::class, 'tarif'])->name('tarif');
        Route::get('tarif/cetak', [AdminLaporanController::class, 'cetakTarif'])->name('tarif.cetak');
        Route::get('/tagihan', [AdminLaporanController::class, 'tagihan'])->name('tagihan');
        Route::get('tagihan/cetak', [AdminLaporanController::class, 'cetakTagihan'])->name('tagihan.cetak');
        Route::get('/pembayaran', [AdminLaporanController::class, 'pembayaran'])->name('pembayaran');
        Route::get('pembayaran/cetak', [AdminLaporanController::class, 'cetakPembayaran'])->name('pembayaran.cetak');
    });
}); //TUTUP GROUP ADMIN

// ========================================
// ROUTE PELANGGAN
// ========================================
Route::prefix('pelanggan')->name('pelanggan.')->middleware(['auth', 'UserAccess:pelanggan'])->group(function() {
    // Dashboard
    Route::prefix('dashboard')->name('dashboard.')->controller(PelangganDashboardController::class)->group(function() {
        Route::get('/', 'index')->name('index');
    });
    
    // TAGIHAN ROUTES
    Route::prefix('tagihan')->name('tagihan.')->group(function() {
        Route::get('/', [PelangganTagihanController::class, 'index'])->name('index');
        Route::get('/{id}', [PelangganTagihanController::class, 'show'])->name('show');
        Route::get('/{id}/download-pdf', [PelangganTagihanController::class, 'downloadPdf'])->name('download-pdf');
        Route::get('/api/statistics', [PelangganTagihanController::class, 'getStatistics'])->name('statistics');
        Route::get('/{id}/bayar', [PelangganBayarController::class, 'bayar'])->name('bayar');
    });
    
    // BAYAR ROUTES - untuk proses pembayaran dan callback internal
    Route::prefix('bayar')->name('bayar.')->group(function() {
        // Route untuk cancel pembayaran
        Route::post('/{id}/cancel', [PelangganBayarController::class, 'cancelPembayaran'])->name('cancel');

        // Route untuk check status - OPTIONAL parameter
        Route::get('/status/{orderId?}', [PelangganBayarController::class, 'checkPaymentStatus'])->name('check-status');
        
        // Route untuk manual check dari frontend
        Route::post('/manual-check', [PelangganBayarController::class, 'manualCheckPayment'])->name('manual-check');
    });
     
    // RIWAYAT ROUTES
    Route::resource('riwayat', PelangganRiwayatController::class);
    
    Route::prefix('riwayat')->name('riwayat.')->group(function() {
        Route::get('riwayat/cetak', [PelangganRiwayatController::class, 'cetak'])->name('cetak');
        Route::get('/{pembayaran}/resubmit', [PelangganRiwayatController::class, 'resubmit'])->name('resubmit');
        Route::delete('/{pembayaran}/cancel', [PelangganRiwayatController::class, 'cancel'])->name('cancel');
    });

    // PROFIL ROUTES
    Route::prefix('profil')->name('profil.')->controller(PelangganProfilController::class)->group(function() {
        Route::get('/', 'index')->name('index');
        Route::get('/edit', 'edit')->name('edit');
        Route::put('/update', 'update')->name('update');
        Route::put('/password', 'updatePassword')->name('password');
        Route::get('/meteran', 'meteran')->name('meteran');
    });
}); // TUTUP GROUP PELANGGAN

// ========================================
// ROUTE SUPERUSER
// ========================================
Route::prefix('superuser')->name('superuser.')->middleware(['auth', 'UserAccess:superuser'])->group(function(){
    Route::prefix('dashboard')->name('dashboard.')->controller(SuperuserDashboardController::class)->group(function(){
        Route::get('/', 'index')->name('index');
    });
    
    // Resource routes untuk users
    Route::resource('users', SuperuserUserController::class);
    Route::resource('meteran', SuperuserMeteranController::class);
    Route::get('meteran/{meteran}/delete', [SuperuserMeteranController::class, 'delete'])->name('meteran.delete');
    Route::resource('pembayaran', SuperuserPembayaranController::class);
    Route::resource('tarif', SuperuserTarifController::class);
    Route::get('tarif/{tarif}/delete', [SuperuserTarifController::class, 'delete'])->name('tarif.delete');
    Route::resource('tagihan', SuperuserTagihanController::class);
    Route::get('tagihan/{tagihan}/delete', [SuperuserTagihanController::class, 'delete'])->name('tagihan.delete');

    // Route untuk activate dan deactivate - menggunakan ID parameter
    Route::post('users/{id}/activate', [SuperuserUserController::class, 'activate'])->name('users.activate');
    Route::post('users/{id}/deactivate', [SuperuserUserController::class, 'deactivate'])->name('users.deactivate');
    
    // Route untuk hard delete (hapus permanen)
    Route::delete('users/{id}/force-delete', [SuperuserUserController::class, 'forceDelete'])->name('users.force-delete');
}); // TUTUP GROUP SUPERUSER


// ========================================
// ROUTE PETUGAS
// ========================================

Route::prefix('petugas')->name('petugas.')->middleware(['auth', 'UserAccess:petugas'])->group(function(){
    Route::prefix('dashboard')->name('dashboard.')->controller(PetugasDashboardController::class)->group(function(){
        Route::get('/', 'index')->name('index');
    });
    Route::resource('meteran', PetugasMeteranController::class)->only([
        'index', 'create', 'store', 'show'
    ]);
    Route::resource('pembacaan', PetugasPembacaanController::class);
});

// ========================================
// ROUTE TAMBAHAN UNTUK TESTING DAN MAINTENANCE
// ========================================

// Route untuk test koneksi database dan model
Route::get('/test-database', function() {
    try {
        $data = [
            'users_count' => \App\Models\User::count(),
            'tagihan_count' => \App\Models\Tagihan::count(),
            'pembayaran_count' => \App\Models\Pembayaran::count(),
            'meteran_count' => \App\Models\Meteran::count(),
        ];
        
        // Test relasi
        $sampleTagihan = \App\Models\Tagihan::with(['pembayaran', 'meteran'])->first();
        if ($sampleTagihan) {
            $data['sample_tagihan'] = [
                'id' => $sampleTagihan->id,
                'status' => $sampleTagihan->status,
                'has_pembayaran' => $sampleTagihan->pembayaran ? true : false,
                'pembayaran_status' => $sampleTagihan->pembayaran->status ?? null,
                'meteran_exists' => $sampleTagihan->meteran ? true : false,
            ];
        }
        
        return response()->json([
            'database_connection' => 'success',
            'data' => $data,
            'timestamp' => now()->toISOString()
        ], 200, [], JSON_PRETTY_PRINT);
        
    } catch (\Exception $e) {
        return response()->json([
            'database_connection' => 'failed',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500, [], JSON_PRETTY_PRINT);
    }
})->middleware('auth');

// Route untuk clear cache application
Route::get('/clear-cache', function() {
    try {
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        
        return response()->json([
            'cache_clear' => 'success',
            'message' => 'All caches cleared successfully',
            'timestamp' => now()->toISOString()
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'cache_clear' => 'failed',
            'error' => $e->getMessage()
        ], 500);
    }
})->middleware('auth');

// ========================================
// FALLBACK ROUTES
// ========================================

// Route untuk handle 404 errors
Route::fallback(function () {
    if (request()->expectsJson()) {
        return response()->json([
            'error' => 'Route not found',
            'message' => 'The requested route does not exist',
            'requested_url' => request()->url(),
            'method' => request()->method()
        ], 404);
    }
    
    return view('errors.404');
});