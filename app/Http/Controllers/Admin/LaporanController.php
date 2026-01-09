<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Meteran;
use App\Models\Tarif;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Laporan Pengguna - Report on users
     */
    public function index(Request $request)
    {
        try {
            $search = $request->get('search');
            $status = $request->get('status');
            $tanggal_dari = $request->get('tanggal_dari');
            $tanggal_sampai = $request->get('tanggal_sampai');
            
            // Query pengguna dengan relasi meteran - HANYA PELANGGAN
            $query = User::withTrashed()
                        ->with(['meteran'])
                        ->where('role', 'pelanggan')
                        ->latest();
            
            // Filter berdasarkan search
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('nama', 'LIKE', '%' . $search . '%')
                    ->orWhere('email', 'LIKE', '%' . $search . '%')
                    ->orWhere('alamat', 'LIKE', '%' . $search . '%')
                    ->orWhere('telepon', 'LIKE', '%' . $search . '%')
                    ->orWhere('no_hp', 'LIKE', '%' . $search . '%');
                });
            }
            
            // Filter berdasarkan status (active/inactive)
            if (!empty($status)) {
                if ($status === 'active') {
                    $query->whereNull('deleted_at');
                } elseif ($status === 'inactive') {
                    $query->whereNotNull('deleted_at');
                }
            }
            
            // Filter berdasarkan rentang tanggal
            if (!empty($tanggal_dari)) {
                $query->whereDate('created_at', '>=', $tanggal_dari);
            }
            
            if (!empty($tanggal_sampai)) {
                $query->whereDate('created_at', '<=', $tanggal_sampai);
            }
            
            $users = $query->paginate(15);
            
            // Append parameters to pagination
            $users->appends($request->all());
            
            // Statistik pengguna - HANYA PELANGGAN
            $statistik = [
                'total_users' => User::withTrashed()->where('role', 'pelanggan')->count(),
                'users_active' => User::whereNull('deleted_at')->where('role', 'pelanggan')->count(),
                'users_inactive' => User::onlyTrashed()->where('role', 'pelanggan')->count(),
                'users_bulan_ini' => User::where('role', 'pelanggan')
                                        ->whereMonth('created_at', Carbon::now()->month)
                                        ->whereYear('created_at', Carbon::now()->year)
                                        ->count(),
                'users_dengan_meteran' => User::where('role', 'pelanggan')->whereHas('meteran')->count(),
                'users_tanpa_meteran' => User::where('role', 'pelanggan')
                                            ->whereDoesntHave('meteran')
                                            ->count()
            ];
            
            return view('pages.admin.laporan.pengguna.index', compact(
                'users', 
                'search', 
                'status', 
                'tanggal_dari', 
                'tanggal_sampai',
                'statistik'
            ));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading user report: ' . $e->getMessage());
        }
    }

    /**
     * Show user details
     */
    public function show(User $user)
    {
        // Load relasi untuk detail user
        $user->load(['meteran', 'meteran.tagihan', 'meteran.tagihan.pembayaran']);
        
        // Hitung statistik user
        $userStats = [];
        if ($user->meteran && $user->meteran->isNotEmpty()) {
            $meteran = $user->meteran->first();
            $userStats = [
                'total_tagihan' => $meteran->tagihan->count(),
                'tagihan_lunas' => $meteran->tagihan->where('status', 'sudah_bayar')->count(),
                'tagihan_belum_bayar' => $meteran->tagihan->where('status', 'belum_bayar')->count(),
                'total_pembayaran' => $meteran->tagihan->sum('total_tagihan'),
                'pembayaran_lunas' => $meteran->tagihan->where('status', 'sudah_bayar')->sum('total_tagihan'),
                'meteran_number' => $meteran->nomor_meteran,
                'meteran_status' => $meteran->status
            ];
        }
        
        // For AJAX request, return JSON
        if (request()->wantsJson()) {
            return response()->json([
                'user' => $user,
                'stats' => $userStats
            ]);
        }
        
        return view('pages.admin.laporan.pengguna.show', compact('user', 'userStats'));
    }

    /**
     * Laporan Meteran - Report on water meters
     */
    public function meteran(Request $request)
    {
        try {
            $search = $request->get('search');
            $status = $request->get('status');
            $tanggal_dari = $request->get('tanggal_dari');
            $tanggal_sampai = $request->get('tanggal_sampai');
            
            // Query meteran dengan relasi user
            $query = Meteran::with(['user'])->latest();
            
            // Filter berdasarkan search
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('nomor_meteran', 'LIKE', '%' . $search . '%')
                      ->orWhere('alamat', 'LIKE', '%' . $search . '%')
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('nama', 'LIKE', '%' . $search . '%')
                                   ->orWhere('name', 'LIKE', '%' . $search . '%')
                                   ->orWhere('email', 'LIKE', '%' . $search . '%');
                      });
                });
            }
            
            // Filter berdasarkan status
            if (!empty($status)) {
                $query->where('status', $status);
            }
            
            // Filter berdasarkan rentang tanggal
            if (!empty($tanggal_dari)) {
                $query->whereDate('created_at', '>=', $tanggal_dari);
            }
            
            if (!empty($tanggal_sampai)) {
                $query->whereDate('created_at', '<=', $tanggal_sampai);
            }
            
            $meteran = $query->paginate(15);
            
            // Append parameters to pagination
            $meteran->appends($request->all());
            
            // Statistik meteran
            $statistik = [
                'total_meteran' => Meteran::count(),
                'meteran_aktif' => Meteran::where('status', 'aktif')->count(),
                'meteran_nonaktif' => Meteran::where('status', 'nonaktif')->count(),
                'meteran_bulan_ini' => Meteran::whereMonth('created_at', Carbon::now()->month)
                                              ->whereYear('created_at', Carbon::now()->year)
                                              ->count()
            ];
            
            return view('pages.admin.laporan.meteran.index', compact(
                'meteran', 
                'search', 
                'status', 
                'tanggal_dari', 
                'tanggal_sampai',
                'statistik'
            ));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading meteran report: ' . $e->getMessage());
        }
    }

    /**
     * Laporan Tarif - Report on water tariffs
     */
    public function tarif(Request $request)
    {
        try {
            $search = $request->get('search');
            $tanggal_dari = $request->get('tanggal_dari');
            $tanggal_sampai = $request->get('tanggal_sampai');
            
            // Query tarif
            $query = Tarif::latest();
            
            // Filter berdasarkan search
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('nama_tarif', 'LIKE', '%' . $search . '%')
                      ->orWhere('deskripsi', 'LIKE', '%' . $search . '%');
                });
            }
            
            // Filter berdasarkan rentang tanggal
            if (!empty($tanggal_dari)) {
                $query->whereDate('created_at', '>=', $tanggal_dari);
            }
            
            if (!empty($tanggal_sampai)) {
                $query->whereDate('created_at', '<=', $tanggal_sampai);
            }
            
            $tarif = $query->paginate(15);
            $tarif->appends($request->all());
            
            // Statistik tarif - PERBAIKAN: Gunakan kolom 'harga' bukan 'harga_per_m3'
            $statistik = [
                'total_tarif' => Tarif::count(),
                'tarif_terendah' => Tarif::min('harga') ?? 0,
                'tarif_tertinggi' => Tarif::max('harga') ?? 0,
                'rata_rata_tarif' => Tarif::avg('harga') ?? 0
            ];
            
            return view('pages.admin.laporan.tarif.index', compact(
                'tarif', 
                'search', 
                'tanggal_dari', 
                'tanggal_sampai',
                'statistik'
            ));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading tarif report: ' . $e->getMessage());
        }
    }

    /**
     * Laporan Tagihan - Report on bills
     */
    public function tagihan(Request $request)
    {
        try {
            $search = $request->get('search');
            $status = $request->get('status');
            $bulan = $request->get('bulan');
            $tahun = $request->get('tahun');
            $tanggal_dari = $request->get('tanggal_dari');
            $tanggal_sampai = $request->get('tanggal_sampai');
            
            // Query tagihan dengan relasi
            $query = Tagihan::with(['meteran.user', 'pembayaran'])->latest();
            
            // Filter berdasarkan search
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('meteran.user', function($userQuery) use ($search) {
                        $userQuery->where('nama', 'LIKE', '%' . $search . '%')
                                 ->orWhere('name', 'LIKE', '%' . $search . '%')
                                 ->orWhere('email', 'LIKE', '%' . $search . '%');
                    })
                    ->orWhereHas('meteran', function($meteranQuery) use ($search) {
                        $meteranQuery->where('nomor_meteran', 'LIKE', '%' . $search . '%');
                    });
                });
            }
            
            // Filter berdasarkan status
            if (!empty($status)) {
                $query->where('status', $status);
            }
            
            // Filter berdasarkan bulan
            if (!empty($bulan)) {
                $query->where('bulan', $bulan);
            }
            
            // Filter berdasarkan tahun
            if (!empty($tahun)) {
                $query->where('tahun', $tahun);
            }
            
            // Filter berdasarkan rentang tanggal
            if (!empty($tanggal_dari)) {
                $query->whereDate('created_at', '>=', $tanggal_dari);
            }
            
            if (!empty($tanggal_sampai)) {
                $query->whereDate('created_at', '<=', $tanggal_sampai);
            }
            
            $tagihan = $query->paginate(15);
            $tagihan->appends($request->all());
            
            // Statistik tagihan
            $statistik = [
                'total_tagihan' => Tagihan::count(),
                'belum_bayar' => Tagihan::where('status', 'belum_bayar')->count(),
                'sudah_bayar' => Tagihan::where('status', 'sudah_bayar')->count(),
                'terlambat' => Tagihan::where('status', 'terlambat')->count(),
                'total_nilai_tagihan' => Tagihan::sum('total_tagihan'),
                'nilai_terbayar' => Tagihan::where('status', 'sudah_bayar')->sum('total_tagihan'),
                'nilai_belum_bayar' => Tagihan::where('status', 'belum_bayar')->sum('total_tagihan')
            ];
            
            return view('pages.admin.laporan.tagihan.index', compact(
                'tagihan', 
                'search', 
                'status', 
                'bulan',
                'tahun',
                'tanggal_dari', 
                'tanggal_sampai',
                'statistik'
            ));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading tagihan report: ' . $e->getMessage());
        }
    }

    /**
     * Laporan Pembayaran - Report on payments
     */
    public function pembayaran(Request $request)
    {
        try {
            $search = $request->get('search');
            $status = $request->get('status');
            $metode = $request->get('metode');
            $tanggal_dari = $request->get('tanggal_dari');
            $tanggal_sampai = $request->get('tanggal_sampai');
            
            // Query pembayaran dengan relasi
            $query = Pembayaran::with(['tagihan.meteran.user'])->latest();
            
            // Filter berdasarkan search
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('transaction_id', 'LIKE', '%' . $search . '%')
                      ->orWhere('order_id', 'LIKE', '%' . $search . '%')
                      ->orWhereHas('tagihan.meteran.user', function($userQuery) use ($search) {
                          $userQuery->where('nama', 'LIKE', '%' . $search . '%')
                                   ->orWhere('name', 'LIKE', '%' . $search . '%')
                                   ->orWhere('email', 'LIKE', '%' . $search . '%');
                      });
                });
            }
            
            // Filter berdasarkan status
            if (!empty($status)) {
                $query->where('status', $status);
            }
            
            // Filter berdasarkan metode pembayaran
            if (!empty($metode)) {
                $query->where('metode_pembayaran', $metode);
            }
            
            // Filter berdasarkan rentang tanggal
            if (!empty($tanggal_dari)) {
                $query->whereDate('tanggal_pembayaran', '>=', $tanggal_dari);
            }
            
            if (!empty($tanggal_sampai)) {
                $query->whereDate('tanggal_pembayaran', '<=', $tanggal_sampai);
            }
            
            $pembayaran = $query->paginate(15);
            $pembayaran->appends($request->all());
            
            // Statistik pembayaran
            $statistik = [
                'total_pembayaran' => Pembayaran::count(),
                'pembayaran_lunas' => Pembayaran::where('status', 'lunas')->count(),
                'pembayaran_pending' => Pembayaran::where('status', 'pending')->count(),
                'pembayaran_gagal' => Pembayaran::where('status', 'failed')->count(),
                'total_nilai_pembayaran' => Pembayaran::where('status', 'lunas')->sum('jumlah_bayar'),
                'pembayaran_hari_ini' => Pembayaran::whereDate('tanggal_pembayaran', Carbon::today())->count(),
                'nilai_hari_ini' => Pembayaran::whereDate('tanggal_pembayaran', Carbon::today())
                                             ->where('status', 'lunas')
                                             ->sum('jumlah_bayar')
            ];
            
            // Statistik per metode pembayaran
            $statistik_metode = Pembayaran::selectRaw('metode_pembayaran, COUNT(*) as total, SUM(jumlah_bayar) as total_nilai')
                                         ->where('status', 'lunas')
                                         ->groupBy('metode_pembayaran')
                                         ->get();

            $metode_pembayaran_list = Pembayaran::distinct('metode_pembayaran')
                                            ->pluck('metode_pembayaran')
                                            ->toArray();
            
            return view('pages.admin.laporan.pembayaran.index', compact(
                'pembayaran', 
                'search', 
                'status', 
                'metode',
                'tanggal_dari', 
                'tanggal_sampai',
                'statistik',
                'statistik_metode',
                'metode_pembayaran_list',
            ));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading pembayaran report: ' . $e->getMessage());
        }
    }

    public function cetak(Request $request)
    {
        try {
            $search = $request->get('search');
            $status = $request->get('status');
            $tanggal_dari = $request->get('tanggal_dari');
            $tanggal_sampai = $request->get('tanggal_sampai');
            
            // Query pengguna dengan relasi meteran - HANYA PELANGGAN
            $query = User::withTrashed()
                        ->with(['meteran'])
                        ->where('role', 'pelanggan')
                        ->latest();
            
            // Apply same filters as index method
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('nama', 'LIKE', '%' . $search . '%')
                    ->orWhere('email', 'LIKE', '%' . $search . '%')
                    ->orWhere('alamat', 'LIKE', '%' . $search . '%')
                    ->orWhere('telepon', 'LIKE', '%' . $search . '%')
                    ->orWhere('no_hp', 'LIKE', '%' . $search . '%');
                });
            }
            
            if (!empty($status)) {
                if ($status === 'active') {
                    $query->whereNull('deleted_at');
                } elseif ($status === 'inactive') {
                    $query->whereNotNull('deleted_at');
                }
            }
            
            if (!empty($tanggal_dari)) {
                $query->whereDate('created_at', '>=', $tanggal_dari);
            }
            
            if (!empty($tanggal_sampai)) {
                $query->whereDate('created_at', '<=', $tanggal_sampai);
            }
            
            // Get all results for print (no pagination)
            $users = $query->get();
            
            // Convert to paginated collection for compatibility with view
            $perPage = 50; // Show more items per page for print
            $currentPage = $request->get('page', 1);
            $users = new \Illuminate\Pagination\LengthAwarePaginator(
                $users->forPage($currentPage, $perPage),
                $users->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            
            // Statistik pengguna
            $statistik = [
                'total_users' => User::withTrashed()->where('role', 'pelanggan')->count(),
                'users_active' => User::whereNull('deleted_at')->where('role', 'pelanggan')->count(),
                'users_inactive' => User::onlyTrashed()->where('role', 'pelanggan')->count(),
                'users_bulan_ini' => User::where('role', 'pelanggan')
                                        ->whereMonth('created_at', Carbon::now()->month)
                                        ->whereYear('created_at', Carbon::now()->year)
                                        ->count(),
                'users_dengan_meteran' => User::where('role', 'pelanggan')->whereHas('meteran')->count(),
            ];
            
            return view('pages.admin.laporan.pengguna.cetak', compact(
                'users', 
                'search', 
                'status', 
                'tanggal_dari', 
                'tanggal_sampai',
                'statistik'
            ));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading print preview: ' . $e->getMessage());
        }
    }

    /**
     * Dashboard statistik
     */
    public function dashboard()
    {
        $statistik = [
            'total_users' => User::count(),
            'active_users' => User::whereNull('deleted_at')->count(),
            'inactive_users' => User::onlyTrashed()->count(),
            'total_meteran' => Meteran::count(),
            'total_tagihan' => Tagihan::count(),
            'total_pembayaran' => Pembayaran::count(),
            'revenue_bulan_ini' => Pembayaran::where('status', 'lunas')
                                           ->whereMonth('tanggal_pembayaran', Carbon::now()->month)
                                           ->whereYear('tanggal_pembayaran', Carbon::now()->year)
                                           ->sum('jumlah_bayar')
        ];
        
        // Statistik per role
        $statistik_role = User::selectRaw('role, COUNT(*) as total')
                             ->groupBy('role')
                             ->get();
        
        // Pengguna terbaru (5 terakhir)
        $users_terbaru = User::with('meteran')
                            ->latest()
                            ->limit(5)
                            ->get();

        if (request()->wantsJson()) {
            return response()->json([
                'statistik' => $statistik,
                'statistik_role' => $statistik_role,
                'users_terbaru' => $users_terbaru
            ]);
        }
        
        return view('pages.admin.laporan.dashboard', compact(
            'statistik',
            'statistik_role', 
            'users_terbaru'
        ));
    }
    private function getFilteredUserData($request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $tanggal_dari = $request->get('tanggal_dari');
        $tanggal_sampai = $request->get('tanggal_sampai');
        
        $query = User::withTrashed()
                    ->with(['meteran'])
                    ->where('role', 'pelanggan'); // Only pelanggan
        
        // Apply same filters as index method
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                ->orWhere('nama', 'LIKE', '%' . $search . '%')
                ->orWhere('email', 'LIKE', '%' . $search . '%')
                ->orWhere('alamat', 'LIKE', '%' . $search . '%')
                ->orWhere('telepon', 'LIKE', '%' . $search . '%')
                ->orWhere('no_hp', 'LIKE', '%' . $search . '%');
            });
        }
        
        if (!empty($status)) {
            if ($status === 'active') {
                $query->whereNull('deleted_at');
            } elseif ($status === 'inactive') {
                $query->whereNotNull('deleted_at');
            }
        }
        
        if (!empty($tanggal_dari)) {
            $query->whereDate('created_at', '>=', $tanggal_dari);
        }
        
        if (!empty($tanggal_sampai)) {
            $query->whereDate('created_at', '<=', $tanggal_sampai);
        }
        
        return $query->get();
    }

    private function getUserStatistik($request)
    {
        return [
            'total_users' => User::withTrashed()->where('role', 'pelanggan')->count(),
            'users_active' => User::whereNull('deleted_at')->where('role', 'pelanggan')->count(),
            'users_inactive' => User::onlyTrashed()->where('role', 'pelanggan')->count(),
            'users_bulan_ini' => User::where('role', 'pelanggan')
                                    ->whereMonth('created_at', Carbon::now()->month)
                                    ->whereYear('created_at', Carbon::now()->year)
                                    ->count(),
            'users_dengan_meteran' => User::where('role', 'pelanggan')->whereHas('meteran')->count(),
        ];
    }

    private function getFilteredMeteranData($request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $tanggal_dari = $request->get('tanggal_dari');
        $tanggal_sampai = $request->get('tanggal_sampai');
        
        $query = Meteran::with(['user']);
        
        // Apply same filters as meteran method
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nomor_meteran', 'LIKE', '%' . $search . '%')
                  ->orWhere('alamat', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('nama', 'LIKE', '%' . $search . '%')
                               ->orWhere('name', 'LIKE', '%' . $search . '%')
                               ->orWhere('email', 'LIKE', '%' . $search . '%');
                  });
            });
        }
        
        if (!empty($status)) {
            $query->where('status', $status);
        }
        
        if (!empty($tanggal_dari)) {
            $query->whereDate('created_at', '>=', $tanggal_dari);
        }
        
        if (!empty($tanggal_sampai)) {
            $query->whereDate('created_at', '<=', $tanggal_sampai);
        }
        
        return $query->get();
    }

    private function getMeteranStatistik($request)
    {
        return [
            'total_meteran' => Meteran::count(),
            'meteran_aktif' => Meteran::where('status', 'aktif')->count(),
            'meteran_nonaktif' => Meteran::where('status', 'nonaktif')->count(),
            'meteran_bulan_ini' => Meteran::whereMonth('created_at', Carbon::now()->month)
                                          ->whereYear('created_at', Carbon::now()->year)
                                          ->count()
        ];
    }

    private function getFilteredTarifData($request)
    {
        $search = $request->get('search');
        $tanggal_dari = $request->get('tanggal_dari');
        $tanggal_sampai = $request->get('tanggal_sampai');
        
        $query = Tarif::query();
        
        // Apply same filters as tarif method
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nama_tarif', 'LIKE', '%' . $search . '%')
                  ->orWhere('deskripsi', 'LIKE', '%' . $search . '%');
            });
        }
        
        if (!empty($tanggal_dari)) {
            $query->whereDate('created_at', '>=', $tanggal_dari);
        }
        
        if (!empty($tanggal_sampai)) {
            $query->whereDate('created_at', '<=', $tanggal_sampai);
        }
        
        return $query->get();
    }

    private function getTarifStatistik($request)
    {
        return [
            'total_tarif' => Tarif::count(),
            'tarif_terendah' => Tarif::min('harga') ?? 0,
            'tarif_tertinggi' => Tarif::max('harga') ?? 0,
            'rata_rata_tarif' => Tarif::avg('harga') ?? 0
        ];
    }

    private function getFilteredTagihanData($request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $tanggal_dari = $request->get('tanggal_dari');
        $tanggal_sampai = $request->get('tanggal_sampai');
        
        $query = Tagihan::with(['meteran.user', 'pembayaran']);
        
        // Apply same filters as tagihan method
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->whereHas('meteran.user', function($userQuery) use ($search) {
                    $userQuery->where('nama', 'LIKE', '%' . $search . '%')
                             ->orWhere('name', 'LIKE', '%' . $search . '%')
                             ->orWhere('email', 'LIKE', '%' . $search . '%');
                })
                ->orWhereHas('meteran', function($meteranQuery) use ($search) {
                    $meteranQuery->where('nomor_meteran', 'LIKE', '%' . $search . '%');
                });
            });
        }
        
        if (!empty($status)) {
            $query->where('status', $status);
        }
        
        if (!empty($bulan)) {
            $query->where('bulan', $bulan);
        }
        
        if (!empty($tahun)) {
            $query->where('tahun', $tahun);
        }
        
        if (!empty($tanggal_dari)) {
            $query->whereDate('created_at', '>=', $tanggal_dari);
        }
        
        if (!empty($tanggal_sampai)) {
            $query->whereDate('created_at', '<=', $tanggal_sampai);
        }
        
        return $query->get();
    }

    private function getTagihanStatistik($request)
    {
        return [
            'total_tagihan' => Tagihan::count(),
            'belum_bayar' => Tagihan::where('status', 'belum_bayar')->count(),
            'sudah_bayar' => Tagihan::where('status', 'sudah_bayar')->count(),
            'terlambat' => Tagihan::where('status', 'terlambat')->count(),
            'total_nilai_tagihan' => Tagihan::sum('total_tagihan'),
            'nilai_terbayar' => Tagihan::where('status', 'sudah_bayar')->sum('total_tagihan'),
            'nilai_belum_bayar' => Tagihan::where('status', 'belum_bayar')->sum('total_tagihan')
        ];
    }

    private function getFilteredPembayaranData($request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $metode = $request->get('metode');
        $tanggal_dari = $request->get('tanggal_dari');
        $tanggal_sampai = $request->get('tanggal_sampai');
        
        $query = Pembayaran::with(['tagihan.meteran.user']);
        
        // Apply same filters as pembayaran method
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('transaction_id', 'LIKE', '%' . $search . '%')
                  ->orWhere('order_id', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('tagihan.meteran.user', function($userQuery) use ($search) {
                      $userQuery->where('nama', 'LIKE', '%' . $search . '%')
                               ->orWhere('name', 'LIKE', '%' . $search . '%')
                               ->orWhere('email', 'LIKE', '%' . $search . '%');
                  });
            });
        }
        
        if (!empty($status)) {
            $query->where('status', $status);
        }
        
        if (!empty($metode)) {
            $query->where('metode_pembayaran', $metode);
        }
        
        if (!empty($tanggal_dari)) {
            $query->whereDate('tanggal_pembayaran', '>=', $tanggal_dari);
        }
        
        if (!empty($tanggal_sampai)) {
            $query->whereDate('tanggal_pembayaran', '<=', $tanggal_sampai);
        }
        
        return $query->get();
    }

    private function getPembayaranStatistik($request)
    {
        return [
            'total_pembayaran' => Pembayaran::count(),
            'pembayaran_lunas' => Pembayaran::where('status', 'lunas')->count(),
            'pembayaran_pending' => Pembayaran::where('status', 'pending')->count(),
            'pembayaran_gagal' => Pembayaran::where('status', 'failed')->count(),
            'total_nilai_pembayaran' => Pembayaran::where('status', 'lunas')->sum('jumlah_bayar'),
        ];
    }
public function cetakMeteran(Request $request)
{
    try {
        $search = $request->get('search');
        $status = $request->get('status');
        $tanggal_dari = $request->get('tanggal_dari');
        $tanggal_sampai = $request->get('tanggal_sampai');
        
        // Query meteran dengan relasi user
        $query = Meteran::with(['user'])->latest();
        
        // Apply same filters as meteran method
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nomor_meteran', 'LIKE', '%' . $search . '%')
                ->orWhere('alamat', 'LIKE', '%' . $search . '%')
                ->orWhereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('nama', 'LIKE', '%' . $search . '%')
                            ->orWhere('name', 'LIKE', '%' . $search . '%')
                            ->orWhere('email', 'LIKE', '%' . $search . '%');
                });
            });
        }
        
        if (!empty($status)) {
            $query->where('status', $status);
        }
        
        if (!empty($tanggal_dari)) {
            $query->whereDate('created_at', '>=', $tanggal_dari);
        }
        
        if (!empty($tanggal_sampai)) {
            $query->whereDate('created_at', '<=', $tanggal_sampai);
        }
        
        // Get all results for print (no pagination)
        $meteran = $query->get();
        
        // Convert to paginated collection for compatibility with view
        $perPage = 50; // Show more items per page for print
        $currentPage = $request->get('page', 1);
        $meteran = new \Illuminate\Pagination\LengthAwarePaginator(
            $meteran->forPage($currentPage, $perPage),
            $meteran->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        // Statistik meteran
        $statistik = [
            'total_meteran' => Meteran::count(),
            'meteran_aktif' => Meteran::where('status', 'aktif')->count(),
            'meteran_nonaktif' => Meteran::where('status', 'nonaktif')->count(),
            'meteran_bulan_ini' => Meteran::whereMonth('created_at', Carbon::now()->month)
                                        ->whereYear('created_at', Carbon::now()->year)
                                        ->count()
        ];
        
        return view('pages.admin.laporan.meteran.cetak', compact(
            'meteran', 
            'search', 
            'status', 
            'tanggal_dari', 
            'tanggal_sampai',
            'statistik'
        ));
        
    } catch (\Exception $e) {
        return back()->with('error', 'Error loading print preview: ' . $e->getMessage());
    }
}
    public function cetakTarif(Request $request)
    {
        try {
            $search = $request->get('search');
            $tanggal_dari = $request->get('tanggal_dari');
            $tanggal_sampai = $request->get('tanggal_sampai');
            
            $query = Tarif::latest();
            
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('nama_tarif', 'LIKE', '%' . $search . '%')
                    ->orWhere('deskripsi', 'LIKE', '%' . $search . '%');
                });
            }
            
            if (!empty($tanggal_dari)) {
                $query->whereDate('created_at', '>=', $tanggal_dari);
            }
            
            if (!empty($tanggal_sampai)) {
                $query->whereDate('created_at', '<=', $tanggal_sampai);
            }
            
            $tarif = $query->get();
            
            $statistik = [
                'total_tarif' => Tarif::count(),
                'tarif_terendah' => Tarif::min('harga') ?? 0,
                'tarif_tertinggi' => Tarif::max('harga') ?? 0,
                'rata_rata_tarif' => Tarif::avg('harga') ?? 0
            ];
            
            return view('pages.admin.laporan.tarif.cetak', compact(
                'tarif', 
                'search', 
                'tanggal_dari', 
                'tanggal_sampai',
                'statistik'
            ));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading print preview: ' . $e->getMessage());
        }
    }
    public function cetakTagihan(Request $request)
    {
        try {
            $search = $request->get('search');
            $status = $request->get('status');
            $bulan = $request->get('bulan');
            $tahun = $request->get('tahun');
            $tanggal_dari = $request->get('tanggal_dari');
            $tanggal_sampai = $request->get('tanggal_sampai');
            
            // Query tagihan dengan relasi
            $query = Tagihan::with(['meteran.user', 'pembayaran'])->latest();
            
            // Filter berdasarkan search
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('meteran.user', function($userQuery) use ($search) {
                        $userQuery->where('nama', 'LIKE', '%' . $search . '%')
                                ->orWhere('name', 'LIKE', '%' . $search . '%')
                                ->orWhere('email', 'LIKE', '%' . $search . '%');
                    })
                    ->orWhereHas('meteran', function($meteranQuery) use ($search) {
                        $meteranQuery->where('nomor_meteran', 'LIKE', '%' . $search . '%');
                    });
                });
            }
            
            // Filter berdasarkan status
            if (!empty($status)) {
                $query->where('status', $status);
            }
            
            // Filter berdasarkan bulan
            if (!empty($bulan)) {
                $query->where('bulan', $bulan);
            }
            
            // Filter berdasarkan tahun
            if (!empty($tahun)) {
                $query->where('tahun', $tahun);
            }
            
            // Filter berdasarkan rentang tanggal
            if (!empty($tanggal_dari)) {
                $query->whereDate('created_at', '>=', $tanggal_dari);
            }
            
            if (!empty($tanggal_sampai)) {
                $query->whereDate('created_at', '<=', $tanggal_sampai);
            }
            
            $tagihan = $query->get();
            
            // Statistik tagihan
            $statistik = [
                'total_tagihan' => Tagihan::count(),
                'belum_bayar' => Tagihan::where('status', 'belum_bayar')->count(),
                'sudah_bayar' => Tagihan::where('status', 'sudah_bayar')->count(),
                'terlambat' => Tagihan::where('status', 'terlambat')->count(),
                'total_nilai_tagihan' => Tagihan::sum('total_tagihan'),
                'nilai_terbayar' => Tagihan::where('status', 'sudah_bayar')->sum('total_tagihan'),
                'nilai_belum_bayar' => Tagihan::where('status', 'belum_bayar')->sum('total_tagihan')
            ];
            
            return view('pages.admin.laporan.tagihan.cetak', compact(
                'tagihan', 
                'search', 
                'status', 
                'bulan',
                'tahun',
                'tanggal_dari', 
                'tanggal_sampai',
                'statistik'
            ));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading print preview: ' . $e->getMessage());
        }
    }
    public function cetakPembayaran(Request $request)
    {
        try {
            $search = $request->get('search');
            $status = $request->get('status');
            $metode = $request->get('metode');
            $tanggal_dari = $request->get('tanggal_dari');
            $tanggal_sampai = $request->get('tanggal_sampai');
            
            // Query pembayaran dengan relasi
            $query = Pembayaran::with(['tagihan.meteran.user'])->latest();
            
            // Filter berdasarkan search
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('transaction_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('order_id', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('tagihan.meteran.user', function($userQuery) use ($search) {
                        $userQuery->where('nama', 'LIKE', '%' . $search . '%')
                                ->orWhere('name', 'LIKE', '%' . $search . '%')
                                ->orWhere('email', 'LIKE', '%' . $search . '%');
                    });
                });
            }
            
            // Filter berdasarkan status
            if (!empty($status)) {
                $query->where('status', $status);
            }
            
            // Filter berdasarkan metode pembayaran
            if (!empty($metode)) {
                $query->where('metode_pembayaran', $metode);
            }
            
            // Filter berdasarkan rentang tanggal
            if (!empty($tanggal_dari)) {
                $query->whereDate('tanggal_pembayaran', '>=', $tanggal_dari);
            }
            
            if (!empty($tanggal_sampai)) {
                $query->whereDate('tanggal_pembayaran', '<=', $tanggal_sampai);
            }
            
            $pembayaran = $query->get();

            $pembayaran = $query->paginate(50);
            
            // Statistik pembayaran
            $statistik = [
                'total_pembayaran' => Pembayaran::count(),
                'pembayaran_lunas' => Pembayaran::where('status', 'lunas')->count(),
                'pembayaran_pending' => Pembayaran::where('status', 'pending')->count(),
                'pembayaran_gagal' => Pembayaran::where('status', 'failed')->count(),
                'total_nilai_pembayaran' => Pembayaran::where('status', 'lunas')->sum('jumlah_bayar'),
                'pembayaran_hari_ini' => Pembayaran::whereDate('tanggal_pembayaran', Carbon::today())->count(),
                'nilai_hari_ini' => Pembayaran::whereDate('tanggal_pembayaran', Carbon::today())
                                            ->where('status', 'lunas')
                                            ->sum('jumlah_bayar')
            ];
            
            // Statistik per metode pembayaran
            $statistik_metode = Pembayaran::selectRaw('metode_pembayaran, COUNT(*) as total, SUM(jumlah_bayar) as total_nilai')
                                        ->where('status', 'lunas')
                                        ->groupBy('metode_pembayaran')
                                        ->get();
            
            $metode_pembayaran_list = Pembayaran::distinct('metode_pembayaran')
                                                ->pluck('metode_pembayaran')
                                                ->toArray();
            
            return view('pages.admin.laporan.pembayaran.cetak', compact(
                'pembayaran', 
                'search', 
                'status', 
                'metode',
                'tanggal_dari', 
                'tanggal_sampai',
                'statistik',
                'statistik_metode',
                'metode_pembayaran_list'
            ));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading print preview: ' . $e->getMessage());
        }
    }
}