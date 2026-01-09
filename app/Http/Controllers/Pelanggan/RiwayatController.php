<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Meteran;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use PDF; // Jika menggunakan barryvdh/laravel-dompdf

class RiwayatController extends Controller
{
    /**
     * Display a listing of payment history.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Query dasar untuk riwayat pembayaran
        $query = Pembayaran::with(['tagihan.meteran'])
            ->whereHas('tagihan.meteran', function($query) use ($user) {
                $query->where('user_id', $user->id);
            });

        // Filter berdasarkan status jika ada
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan metode pembayaran jika ada
        if ($request->filled('metode')) {
            $query->where('metode_pembayaran', $request->metode);
        }

        // Filter berdasarkan tahun jika ada
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_pembayaran', $request->tahun);
        }

        // Filter berdasarkan bulan jika ada
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_pembayaran', $request->bulan);
        }

        // Filter berdasarkan rentang tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_pembayaran', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_pembayaran', '<=', $request->tanggal_selesai);
        }

        // Search functionality - hanya untuk keterangan pembayaran dan periode tagihan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('tagihan', function($subQ) use ($search) {
                      $subQ->where('bulan', 'like', "%{$search}%")
                           ->orWhere('tahun', 'like', "%{$search}%");
                  });
            });
        }

        // Filter berdasarkan meteran (jika user memiliki lebih dari 1 meteran)
        if ($request->filled('meteran_id')) {
            $query->whereHas('tagihan.meteran', function($q) use ($request) {
                $q->where('id', $request->meteran_id);
            });
        }

        // Ambil pembayaran dengan pagination
        $pembayaran = $query->orderBy('tanggal_pembayaran', 'desc')->paginate(10);


        $totalPembayaran = Pembayaran::whereHas('tagihan.meteran', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();


        $totalDibayar = Pembayaran::whereHas('tagihan.meteran', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->where('status', 'lunas')
            ->sum('jumlah_bayar');

        $pembayaranPending = Pembayaran::whereHas('tagihan.meteran', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->where('status', 'pending')
            ->count();


        $pembayaranDitolak = Pembayaran::whereHas('tagihan.meteran', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->whereIn('status', ['failed', 'cancelled'])
            ->count();


        $pembayaranBulanIni = Pembayaran::whereHas('tagihan.meteran', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->whereMonth('tanggal_pembayaran', Carbon::now()->month)
            ->whereYear('tanggal_pembayaran', Carbon::now()->year)
            ->where('status', 'lunas')
            ->sum('jumlah_bayar');

        // Ambil tahun-tahun untuk filter dropdown
        $tahunList = Pembayaran::whereHas('tagihan.meteran', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->selectRaw('YEAR(tanggal_pembayaran) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        // Bulan untuk filter dropdown
        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Ambil daftar meteran milik user (jika ada lebih dari 1)
        $meteranList = $user->meteran;

        return view('pages.pelanggan.riwayat.index', compact(
            'pembayaran', 'totalPembayaran', 'totalDibayar', 'pembayaranPending', 
            'pembayaranDitolak', 'pembayaranBulanIni', 'tahunList', 'bulanList', 'meteranList'
        ));
    }

    /**
     * Display the specified payment detail.
     */
    public function show($id)
    {
        $user = Auth::user();
        
        // Ambil pembayaran berdasarkan ID dan pastikan milik user yang sedang login
        $pembayaran = Pembayaran::with(['tagihan.meteran'])
            ->whereHas('tagihan.meteran', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->findOrFail($id);
        
        return view('pages.pelanggan.riwayat.show', compact('pembayaran'));
    }

    /**
     * Get payment statistics for dashboard
     */
    public function getStatistics()
    {
        $user = Auth::user();
        
        $stats = [
            'total_pembayaran' => Pembayaran::whereHas('tagihan.meteran', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count(),
            

            'total_dibayar' => Pembayaran::whereHas('tagihan.meteran', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->where('status', 'lunas')
                ->sum('jumlah_bayar'),
            
            'pembayaran_pending' => Pembayaran::whereHas('tagihan.meteran', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->where('status', 'pending')
                ->count(),
            

            'pembayaran_ditolak' => Pembayaran::whereHas('tagihan.meteran', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->whereIn('status', ['failed', 'cancelled'])
                ->count(),
            

            'pembayaran_bulan_ini' => Pembayaran::whereHas('tagihan.meteran', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->whereMonth('tanggal_pembayaran', Carbon::now()->month)
                ->whereYear('tanggal_pembayaran', Carbon::now()->year)
                ->where('status', 'lunas')
                ->sum('jumlah_bayar'),
            

            'rata_rata_bulanan' => Pembayaran::whereHas('tagihan.meteran', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->where('status', 'lunas')
                ->whereYear('tanggal_pembayaran', Carbon::now()->year)
                ->avg('jumlah_bayar'),
            
            'pembayaran_terbaru' => Pembayaran::whereHas('tagihan.meteran', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->with(['tagihan.meteran'])
                ->orderBy('tanggal_pembayaran', 'desc')
                ->take(5)
                ->get()
        ];
        
        return response()->json($stats);
    }

    /**
     * Export payment history to Excel/CSV
     */

    /**
     * Get monthly payment chart data
     */
    public function getChartData(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->get('tahun', Carbon::now()->year);
        
        $monthlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {

            $total = Pembayaran::whereHas('tagihan.meteran', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->where('status', 'lunas')
                ->whereYear('tanggal_pembayaran', $tahun)
                ->whereMonth('tanggal_pembayaran', $month)
                ->sum('jumlah_bayar');
            
            $monthlyData[] = [
                'bulan' => Carbon::create()->month($month)->format('M'),
                'total' => $total
            ];
        }
        
        return response()->json($monthlyData);
    }

    /**
     * Resubmit rejected payment
     */
    public function resubmit($id)
    {
        $user = Auth::user();
        
        $pembayaran = Pembayaran::with('tagihan')
            ->whereHas('tagihan.meteran', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereIn('status', ['failed', 'cancelled'])
            ->findOrFail($id);

        // Redirect ke form pembayaran ulang
        return redirect()->route('pelanggan.tagihan.bayar', $pembayaran->tagihan_id)
            ->with('info', 'Silakan upload ulang bukti pembayaran untuk tagihan ini.');
    }

    /**
     * Cancel pending payment
     */
    public function cancel($id)
    {
        $user = Auth::user();
        
        $pembayaran = Pembayaran::with('tagihan')
            ->whereHas('tagihan.meteran', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('status', 'pending')
            ->findOrFail($id);

        try {
            // Hapus bukti pembayaran jika ada
            if ($pembayaran->bukti_pembayaran) {
                Storage::disk('public')->delete($pembayaran->bukti_pembayaran);
            }

            // Update status tagihan kembali ke belum bayar atau terlambat
            $tagihan = $pembayaran->tagihan;
            $status = Carbon::parse($tagihan->tanggal_jatuh_tempo)->isPast() ? 'terlambat' : 'belum_bayar';
            $tagihan->update(['status' => $status]);

            // Hapus record pembayaran
            $pembayaran->delete();

            return redirect()->route('pelanggan.riwayat.index')
                ->with('success', 'Pembayaran berhasil dibatalkan.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membatalkan pembayaran. Silakan coba lagi.');
        }
    }
    public function cetak(Request $request)
    {
        $user = Auth::user();
        
        // Query dasar untuk riwayat pembayaran (sama seperti index)
        $query = Pembayaran::with(['tagihan.meteran'])
            ->whereHas('tagihan.meteran', function($query) use ($user) {
                $query->where('user_id', $user->id);
            });

        // Terapkan filter yang sama seperti index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('metode')) {
            $query->where('metode_pembayaran', $request->metode);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_pembayaran', $request->tahun);
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_pembayaran', $request->bulan);
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_pembayaran', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_pembayaran', '<=', $request->tanggal_selesai);
        }

        if ($request->filled('meteran_id')) {
            $query->whereHas('tagihan.meteran', function($q) use ($request) {
                $q->where('id', $request->meteran_id);
            });
        }

        $pembayaran = $query->orderBy('tanggal_pembayaran', 'desc')->paginate(100);

        // Statistik untuk halaman cetak
        $totalPembayaran = $pembayaran->count();
        $totalDibayar = $pembayaran->where('status', 'lunas')->sum('jumlah_bayar');
        $pembayaranPending = $pembayaran->where('status', 'pending')->count();
        $pembayaranDitolak = $pembayaran->whereIn('status', ['failed', 'cancelled'])->count();
        $pembayaranBulanIni = $pembayaran->where('status', 'lunas')
            ->filter(function($item) {
                return Carbon::parse($item->tanggal_pembayaran)->isCurrentMonth();
            })
            ->sum('jumlah_bayar');

        // Bulan list untuk menampilkan nama bulan di filter
        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('pages.pelanggan.riwayat.cetak', compact(
            'pembayaran', 'totalPembayaran', 'totalDibayar', 'pembayaranPending', 
            'pembayaranDitolak', 'pembayaranBulanIni', 'bulanList'
        ));
    }
}