<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\PembacaanMeteran;
use App\Models\Meteran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use PDF;

class TagihanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $tagihan = Tagihan::with(['meteran', 'pembayaran'])
                         ->whereHas('meteran', function($q) use ($user) {
                             $q->where('user_id', $user->id);
                         })
                         ->paginate(15);
        
        $stats = [
            'total_tagihan' => $tagihan->total(),
            'belum_bayar' => 0, // your logic
            'sudah_bayar' => 0, // your logic
        ];
        
        // Update status otomatis
        // $this->updateTagihanStatus($user);

        // $this->syncTagihanWithPembayaran($user);

        // Query dasar untuk tagihan
        $query = Tagihan::with(['meteran', 'pembacaanMeteran', 'pembayaran'])
            ->whereHas('meteran', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->active();

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tahun
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('bulan', 'like', "%{$search}%")
                  ->orWhere('tahun', 'like', "%{$search}%")
                  ->orWhere('nomor_tagihan', 'like', "%{$search}%")
                  ->orWhereHas('meteran', function($subQ) use ($search) {
                      $subQ->where('nomor_meteran', 'like', "%{$search}%")
                           ->orWhere('alamat', 'like', "%{$search}%");
                  });
            });
        }

        // Ambil tagihan dengan pagination
        $tagihan = $query->orderBy('tanggal_tagihan', 'desc')->paginate(10);

        // Statistik untuk dashboard cards
        $stats = $this->getUpdatedStatistics($user);

        // Ambil tahun-tahun untuk filter dropdown
        $tahunList = Tagihan::whereHas('meteran', function($q) use ($user) {
                               $q->where('user_id', $user->id);
                           })
                           ->selectRaw('DISTINCT tahun')
                           ->orderBy('tahun', 'desc')
                           ->pluck('tahun')
                           ->toArray();
        
        // Ensure we have at least current year
        if (empty($tahunList)) {
            $tahunList = [(int) date('Y')];
        }

        return view('pages.pelanggan.tagihan.index', compact(
            'tagihan', 
            'tahunList'
        ) + $stats);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $tagihan = Tagihan::with(['meteran', 'pembacaanMeteran', 'pembayaran'])
            ->whereHas('meteran', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->findOrFail($id);
        
        return view('pages.pelanggan.tagihan.show', compact('tagihan'));
    }

    /**
     * Download tagihan as PDF
     */
    public function downloadPdf($id)
    {
        $user = Auth::user();
        
        $tagihan = Tagihan::with(['meteran', 'pembacaanMeteran', 'pembayaran'])
            ->whereHas('meteran', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('status', Tagihan::STATUS_SUDAH_BAYAR)
            ->findOrFail($id);

        try {
            $pdf = PDF::loadView('pages.pelanggan.tagihan.pdf', compact('tagihan'));
            $fileName = 'Tagihan_' . $tagihan->nomor_tagihan . '.pdf';
            
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            Log::error('PDF Download Error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal mengunduh PDF. Silakan coba lagi.');
        }
    }

    /**
     * Get tagihan statistics for dashboard - AJAX endpoint
     */
    public function getStatistics()
    {
        $user = Auth::user();
        
        // Update status sebelum mengambil statistik
        $this->updateTagihanStatus($user);
        
        $stats = $this->getUpdatedStatistics($user);
        
        return response()->json($stats);
    }

    /**
     * Get updated statistics with proper status checking
     */
    private function getUpdatedStatistics($user)
    {
        // Refresh status semua tagihan user sebelum hitung statistik
        // $this->syncTagihanWithPembayaran($user);
        
        $baseQuery = Tagihan::whereHas('meteran', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->active();

        return [
            'totalTagihan' => (clone $baseQuery)->count(),
            
            'belumBayar' => (clone $baseQuery)->where('status', Tagihan::STATUS_BELUM_BAYAR)->count(),
            
            'sudahBayar' => (clone $baseQuery)->where('status', Tagihan::STATUS_SUDAH_BAYAR)->count(),
            
            'terlambat' => (clone $baseQuery)->where('status', Tagihan::STATUS_TERLAMBAT)->count(),
            
            'menungguKonfirmasi' => (clone $baseQuery)->where('status', Tagihan::STATUS_MENUNGGU_KONFIRMASI)->count(),
            
            // Total hutang dari tagihan yang belum dibayar
            'totalHutang' => $this->calculateActualDebt($user),
            
            'tagihanBulanIni' => (clone $baseQuery)
                ->whereMonth('tanggal_tagihan', Carbon::now()->month)
                ->whereYear('tanggal_tagihan', Carbon::now()->year)
                ->first()
        ];
    }

    /**
     * Calculate actual debt excluding paid bills
     */
    private function calculateActualDebt($user)
    {
        return Tagihan::whereHas('meteran', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->active()
        ->whereIn('status', [
            Tagihan::STATUS_BELUM_BAYAR, 
            Tagihan::STATUS_TERLAMBAT, 
            Tagihan::STATUS_MENUNGGU_KONFIRMASI
        ])
        ->whereDoesntHave('pembayaran', function($q) {
            $q->where('status', Pembayaran::STATUS_PAID);
        })
        ->sum('total_tagihan');
    }

    /**
     * Update status tagihan yang sudah melewati jatuh tempo dan sinkronisasi dengan pembayaran
     */
    private function updateTagihanStatus($user)
    {
        try {
            $today = Carbon::now();
            
            // 1. Update tagihan yang terlambat (belum bayar + lewat jatuh tempo)
            $overdueUpdated = Tagihan::whereHas('meteran', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('status', Tagihan::STATUS_BELUM_BAYAR)
            ->where('tanggal_jatuh_tempo', '<', $today)
            ->update(['status' => Tagihan::STATUS_TERLAMBAT]);
            
            if ($overdueUpdated > 0) {
                Log::info("Updated {$overdueUpdated} overdue bills for user {$user->id}");
            }

            // 2. Sinkronisasi status tagihan dengan pembayaran
            $this->syncTagihanWithPembayaran($user);
            
        } catch (\Exception $e) {
            Log::error('Error updating tagihan status: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Sync tagihan status with actual pembayaran status
     */
    private function syncTagihanWithPembayaran($user)
    {
        try {
            // 1. Update tagihan yang pembayarannya sudah PAID dan bukan manual status
            $paidTagihan = Tagihan::with('pembayaran')
                ->whereHas('meteran', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->whereHas('pembayaran', function($q) {
                    $q->where('status', Pembayaran::STATUS_PAID)
                      ->where('is_verified', true);
                })
                ->where('status', '!=', Tagihan::STATUS_SUDAH_BAYAR)
                ->where(function($query) {
                    // Hanya auto-sync yang bukan manual atau sudah lama
                    $query->where('is_manual_status', false)
                          ->orWhere('manual_updated_at', '<', now()->subHours(24))
                          ->orWhereNull('manual_updated_at');
                })
                ->get();

            foreach ($paidTagihan as $tagihan) {
                if ($tagihan->shouldAutoSync()) {
                    $oldStatus = $tagihan->status;
                    $tagihan->update(['status' => Tagihan::STATUS_SUDAH_BAYAR]);
                    
                    Log::info("Auto-synced paid bill status", [
                        'tagihan_id' => $tagihan->id,
                        'old_status' => $oldStatus,
                        'new_status' => Tagihan::STATUS_SUDAH_BAYAR,
                        'payment_status' => $tagihan->pembayaran->status,
                        'user_id' => $user->id
                    ]);
                }
            }

            // 2. Update tagihan yang pembayarannya PENDING (tetap auto-sync)
            $pendingTagihan = Tagihan::with('pembayaran')
                ->whereHas('meteran', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->whereHas('pembayaran', function($q) {
                    $q->where('status', Pembayaran::STATUS_PENDING);
                })
                ->where('status', '!=', Tagihan::STATUS_MENUNGGU_KONFIRMASI)
                ->get();

            foreach ($pendingTagihan as $tagihan) {
                $oldStatus = $tagihan->status;
                $tagihan->update(['status' => Tagihan::STATUS_MENUNGGU_KONFIRMASI]);
                
                Log::info("Auto-synced pending bill status", [
                    'tagihan_id' => $tagihan->id,
                    'old_status' => $oldStatus,
                    'new_status' => Tagihan::STATUS_MENUNGGU_KONFIRMASI,
                    'payment_status' => $tagihan->pembayaran->status,
                    'user_id' => $user->id
                ]);
            }

            // 3. Handle pembayaran gagal (hanya jika bukan manual status "sudah_bayar")
            $failedTagihan = Tagihan::with('pembayaran')
                ->whereHas('meteran', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->whereHas('pembayaran', function($q) {
                    $q->whereIn('status', [
                        Pembayaran::STATUS_FAILED,
                        Pembayaran::STATUS_EXPIRED,
                        Pembayaran::STATUS_CANCELLED
                    ]);
                })
                ->where(function($query) {
                    // Jangan ubah jika manual set "sudah_bayar"
                    $query->where('status', '!=', Tagihan::STATUS_SUDAH_BAYAR)
                          ->orWhere('is_manual_status', false)
                          ->orWhere('manual_updated_at', '<', now()->subHours(24));
                })
                ->get();

            foreach ($failedTagihan as $tagihan) {
                if ($tagihan->shouldAutoSync() || $tagihan->status !== Tagihan::STATUS_SUDAH_BAYAR) {
                    $shouldBeTerlambat = Carbon::parse($tagihan->tanggal_jatuh_tempo)->isPast();
                    $expectedStatus = $shouldBeTerlambat ? Tagihan::STATUS_TERLAMBAT : Tagihan::STATUS_BELUM_BAYAR;
                    
                    if ($tagihan->status !== $expectedStatus) {
                        $oldStatus = $tagihan->status;
                        $tagihan->update(['status' => $expectedStatus]);
                        
                        Log::info("Updated failed payment tagihan", [
                            'tagihan_id' => $tagihan->id,
                            'old_status' => $oldStatus,
                            'new_status' => $expectedStatus,
                            'payment_status' => $tagihan->pembayaran->status,
                            'user_id' => $user->id
                        ]);
                    }
                }
            }

            // 4. Handle orphaned "sudah_bayar" (hanya jika bukan manual status)
            $orphanedPaidBills = Tagihan::with('pembayaran')
                ->whereHas('meteran', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->where('status', Tagihan::STATUS_SUDAH_BAYAR)
                ->where('is_manual_status', false) // Hanya yang bukan manual
                ->where(function($q) {
                    $q->whereDoesntHave('pembayaran')
                      ->orWhereHas('pembayaran', function($subQ) {
                          $subQ->where('status', '!=', Pembayaran::STATUS_PAID)
                               ->where('is_verified', '!=', true);
                      });
                })
                ->get();

            foreach ($orphanedPaidBills as $tagihan) {
                $shouldBeTerlambat = Carbon::parse($tagihan->tanggal_jatuh_tempo)->isPast();
                $newStatus = $shouldBeTerlambat ? Tagihan::STATUS_TERLAMBAT : Tagihan::STATUS_BELUM_BAYAR;
                
                $tagihan->update(['status' => $newStatus]);
                
                Log::info("Fixed orphaned paid bill (non-manual)", [
                    'tagihan_id' => $tagihan->id,
                    'old_status' => Tagihan::STATUS_SUDAH_BAYAR,
                    'new_status' => $newStatus,
                    'has_pembayaran' => $tagihan->pembayaran ? 'yes' : 'no',
                    'pembayaran_status' => $tagihan->pembayaran->status ?? 'none',
                    'user_id' => $user->id
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error synchronizing tagihan with pembayaran: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }       
}