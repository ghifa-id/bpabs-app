<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PembayaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pembayaran::with([
            'tagihan.meteran.user',
            'tagihan.pembacaanMeteran.meteran.user',
            'verifiedBy'
        ])->orderBy('created_at', 'desc');

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan metode pembayaran
        if ($request->filled('metode')) {
            $query->where('metode_pembayaran', $request->metode);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_pembayaran', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_pembayaran', '<=', $request->tanggal_sampai);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_pembayaran', 'like', "%{$search}%")
                ->orWhere('transaction_id', 'like', "%{$search}%")
                ->orWhere('order_id', 'like', "%{$search}%")
                ->orWhereHas('tagihan', function($subQ) use ($search) {
                    $subQ->where('nomor_tagihan', 'like', "%{$search}%")
                        ->orWhereHas('meteran', function($subSubQ) use ($search) {
                            $subSubQ->where('nomor_meteran', 'like', "%{$search}%")
                                    ->orWhereHas('user', function($userQ) use ($search) {
                                        $userQ->where('nama', 'like', "%{$search}%")
                                                ->orWhere('name', 'like', "%{$search}%")
                                                ->orWhere('email', 'like', "%{$search}%");
                                    });
                        });
                });
            });
        }

        $pembayarans = $query->paginate(15);

        $stats = $this->getStatistics($request);

        return view('pages.admin.pembayaran.index', compact('pembayarans', 'stats'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pembayaran = Pembayaran::with([
            'tagihan.meteran.user',
            'tagihan.pembacaanMeteran.meteran.user',
            'verifiedBy'
        ])->findOrFail($id);

        return view('pages.admin.pembayaran.show', compact('pembayaran'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $tagihan = null;
        
        if ($request->filled('tagihan_id')) {
            $tagihan = Tagihan::with(['meteran.user', 'pembacaanMeteran.meteran.user'])
                ->where('status', '!=', Tagihan::STATUS_SUDAH_BAYAR)
                ->findOrFail($request->tagihan_id);
        }

        $tagihans = Tagihan::with(['meteran.user', 'pembacaanMeteran.meteran.user'])
            ->where('status', '!=', 'sudah_bayar')
            ->orderBy('created_at', 'desc')
            ->get();

        $users = User::where('role', 'pelanggan')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('pages.admin.pembayaran.create', compact('tagihan', 'tagihans', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tagihan_id' => 'required|exists:tagihan,id',
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string|max:100',
            'tanggal_bayar' => 'required|date',
            'keterangan' => 'nullable|string|max:1000',
            'catatan_admin' => 'nullable|string|max:1000'
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $tagihan = Tagihan::lockForUpdate()->findOrFail($request->tagihan_id);

                // Cek apakah tagihan sudah dibayar
                if ($tagihan->status === 'sudah_bayar') {
                    return redirect()->back()
                        ->with('error', 'Tagihan ini sudah dibayar.');
                }

                $pembayaran = new Pembayaran();
                $pembayaran->tagihan_id = $request->tagihan_id;
                $pembayaran->jumlah_bayar = $request->jumlah_bayar;
                $pembayaran->tanggal_pembayaran = $request->tanggal_bayar;
                $pembayaran->metode_pembayaran = $request->metode_pembayaran;
                $pembayaran->status = 'lunas';
                $pembayaran->keterangan = $request->keterangan;
                $pembayaran->catatan_admin = $request->catatan_admin;
                $pembayaran->processed_by = 'admin_' . Auth::id();
                $pembayaran->processed_at = now();
                $pembayaran->is_verified = true;
                $pembayaran->verified_by = Auth::id();
                $pembayaran->verified_at = now();

                $pembayaran->save();

                $this->updateTagihanStatusSafely($tagihan, 'sudah_bayar', $pembayaran->id);

                Log::info('Manual payment created by admin', [
                    'pembayaran_id' => $pembayaran->id,
                    'tagihan_id' => $tagihan->id,
                    'admin_id' => Auth::id(),
                    'amount' => $request->jumlah_bayar,
                    'tagihan_status_updated' => true
                ]);

                return redirect()->route('admin.pembayaran.show', $pembayaran)
                    ->with('success', 'Pembayaran berhasil ditambahkan dan status tagihan telah diperbarui.');
            });

        } catch (\Exception $e) {
            Log::error('Error creating manual payment', [
                'message' => $e->getMessage(),
                'admin_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal menambahkan pembayaran: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $pembayaran = Pembayaran::with(['tagihan.meteran.user'])->findOrFail($id);

        // Hanya bisa edit pembayaran yang belum diverifikasi atau pending
        if ($pembayaran->status === 'lunas' && $pembayaran->is_verified) {
            return redirect()->back()
                ->with('error', 'Pembayaran yang sudah diverifikasi tidak dapat diedit.');
        }

        return view('pages.admin.pembayaran.edit', compact('pembayaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string|max:100',
            'tanggal_pembayaran' => 'required|date',
            'status' => 'required|in:pending,lunas,failed,expired,cancelled',
            'keterangan' => 'nullable|string|max:1000',
            'catatan_admin' => 'nullable|string|max:1000'
        ]);

        try {
            return DB::transaction(function () use ($request, $id) {
                $pembayaran = Pembayaran::with('tagihan')->lockForUpdate()->findOrFail($id);

                // Cek apakah bisa diedit
                if ($pembayaran->status === 'lunas' && $pembayaran->is_verified) {
                    return redirect()->back()
                        ->with('error', 'Pembayaran yang sudah diverifikasi tidak dapat diedit.');
                }

                $oldStatus = $pembayaran->status;

                $pembayaran->jumlah_bayar = $request->jumlah_bayar;
                $pembayaran->metode_pembayaran = $request->metode_pembayaran;
                $pembayaran->tanggal_pembayaran = $request->tanggal_pembayaran;
                $pembayaran->status = $request->status;
                $pembayaran->keterangan = $request->keterangan;
                $pembayaran->catatan_admin = $request->catatan_admin;

                // Update processed info jika status berubah
                if ($oldStatus !== $request->status) {
                    $pembayaran->processed_by = 'admin_' . Auth::id();
                    $pembayaran->processed_at = now();
                }

                $pembayaran->save();

                if ($pembayaran->tagihan) {
                    $tagihan = Tagihan::lockForUpdate()->find($pembayaran->tagihan_id);
                    
                    if ($request->status === 'lunas') {
                        $this->updateTagihanStatusSafely($tagihan, 'sudah_bayar', $pembayaran->id);
                    } else {
                        $this->updateTagihanStatusSafely($tagihan, 'belum_bayar', $pembayaran->id);
                    }
                }

                Log::info('Payment updated by admin', [
                    'pembayaran_id' => $pembayaran->id,
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                    'admin_id' => Auth::id()
                ]);

                return redirect()->route('admin.pembayaran.show', $pembayaran)
                    ->with('success', 'Pembayaran berhasil diperbarui dan status tagihan telah disinkronkan.');
            });

        } catch (\Exception $e) {
            Log::error('Error updating payment', [
                'message' => $e->getMessage(),
                'pembayaran_id' => $id,
                'admin_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal memperbarui pembayaran: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $pembayaran = Pembayaran::with('tagihan')->lockForUpdate()->findOrFail($id);

                $tagihanId = $pembayaran->tagihan_id;
                
                if ($pembayaran->tagihan) {
                    $tagihan = Tagihan::lockForUpdate()->find($pembayaran->tagihan_id);
                    $this->updateTagihanStatusSafely($tagihan, 'belum_bayar', null);
                }

                $pembayaran->delete();

                Log::info('Payment deleted by admin', [
                    'pembayaran_id' => $id,
                    'tagihan_id' => $tagihanId,
                    'admin_id' => Auth::id()
                ]);

                return redirect()->route('admin.pembayaran.index')
                    ->with('success', 'Pembayaran berhasil dihapus dan status tagihan telah diperbarui.');
            });

        } catch (\Exception $e) {
            Log::error('Error deleting payment', [
                'message' => $e->getMessage(),
                'pembayaran_id' => $id,
                'admin_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal menghapus pembayaran: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $pembayaran = Pembayaran::with([
            'tagihan.meteran.user',
            'tagihan.pembacaanMeteran.meteran.user'
        ])->findOrFail($id);

        return view('pages.admin.pembayaran.delete', compact('pembayaran'));
    }

    public function confirmPayment(Request $request, $id)
    {
        // Validate admin note if provided
        $request->validate([
            'admin_note' => 'nullable|string|max:1000'
        ]);

        try {
            return DB::transaction(function () use ($request, $id) {

                $pembayaran = Pembayaran::lockForUpdate()->findOrFail($id);

                if ($pembayaran->is_verified) {
                    return redirect()->back()
                        ->with('info', 'Pembayaran ini sudah diverifikasi.');
                }

                $oldPembayaranStatus = $pembayaran->status;
                $oldTagihanStatus = null;

                // Lock the tagihan for update as well
                $tagihan = null;
                if ($pembayaran->tagihan_id) {
                    $tagihan = Tagihan::lockForUpdate()->find($pembayaran->tagihan_id);
                    $oldTagihanStatus = $tagihan ? $tagihan->status : null;
                }

                // Update pembayaran status
                $pembayaran->is_verified = true;
                $pembayaran->verified_by = Auth::id();
                $pembayaran->verified_at = now();
                
                // Update status jika belum lunas
                if ($pembayaran->status !== 'lunas') {
                    $pembayaran->status = 'lunas';
                    $pembayaran->processed_by = 'admin_verified_' . Auth::id();
                    $pembayaran->processed_at = now();
                }

                // Add admin note if provided
                if ($request->filled('admin_note')) {
                    $existingNote = $pembayaran->catatan_admin;
                    $newNote = $request->admin_note;
                    
                    if ($existingNote) {
                        $pembayaran->catatan_admin = $existingNote . "\n\n[Verifikasi] " . $newNote;
                    } else {
                        $pembayaran->catatan_admin = "[Verifikasi] " . $newNote;
                    }
                }

                // Save pembayaran first
                $pembayaran->save();

                if ($tagihan) {
                    $tagihan->status = 'sudah_bayar';
                    $tagihan->save();
                    $updateResult = $this->updateTagihanStatusSafely($tagihan, 'sudah_bayar', $pembayaran->id);
                    
                    if (!$updateResult) {
                        throw new \Exception('Gagal mengupdate status tagihan setelah beberapa percobaan');
                    }
                } else {
                    Log::warning('No tagihan found for payment confirmation', [
                        'pembayaran_id' => $pembayaran->id
                    ]);
                }
                
                $this->createStatusAuditLog([
                    'pembayaran_id' => $pembayaran->id,
                    'tagihan_id' => $tagihan ? $tagihan->id : null,
                    'action' => 'confirm_payment',
                    'old_pembayaran_status' => $oldPembayaranStatus,
                    'new_pembayaran_status' => $pembayaran->status,
                    'old_tagihan_status' => $oldTagihanStatus,
                    'expected_tagihan_status' => 'sudah_bayar',
                    'tagihan_update_success' => $updateResult ?? false,
                    'admin_id' => Auth::id(),
                    'admin_note' => $request->admin_note ?? null,
                    'timestamp' => now()
                ]);

                Log::info('Payment confirmed by admin', [
                    'pembayaran_id' => $pembayaran->id,
                    'old_pembayaran_status' => $oldPembayaranStatus,
                    'new_pembayaran_status' => $pembayaran->status,
                    'old_tagihan_status' => $oldTagihanStatus,
                    'new_tagihan_status' => $tagihan ? $tagihan->status : null,
                    'admin_id' => Auth::id(),
                    'admin_note' => $request->admin_note ?? null,
                    'tagihan_update_success' => $tagihan ? true : false
                ]);

                return redirect()->route('admin.pembayaran.show', $pembayaran->id)
                    ->with('success', 'Pembayaran berhasil dikonfirmasi dan diverifikasi. Status tagihan telah diperbarui menjadi sudah bayar.');
            });

        } catch (\Exception $e) {
            Log::error('Error confirming payment', [
                'message' => $e->getMessage(),
                'pembayaran_id' => $id,
                'admin_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal mengkonfirmasi pembayaran: ' . $e->getMessage());
        }
    }

    public function rejectPayment(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            return DB::transaction(function () use ($request, $id) {
                $pembayaran = Pembayaran::lockForUpdate()->findOrFail($id);

                // Check if already verified
                if ($pembayaran->is_verified) {
                    return redirect()->back()
                        ->with('error', 'Pembayaran yang sudah diverifikasi tidak dapat ditolak.');
                }

                $oldStatus = $pembayaran->status;
                
                $pembayaran->status = 'failed';
                $pembayaran->catatan_admin = $request->reason;
                $pembayaran->processed_by = 'admin_rejected_' . Auth::id();
                $pembayaran->processed_at = now();
                $pembayaran->save();

                if ($pembayaran->tagihan_id) {
                    $tagihan = Tagihan::lockForUpdate()->find($pembayaran->tagihan_id);
                    if ($tagihan) {
                        $this->updateTagihanStatusSafely($tagihan, 'belum_bayar', $pembayaran->id);
                    }
                }

                Log::info('Payment rejected by admin', [
                    'pembayaran_id' => $pembayaran->id,
                    'old_status' => $oldStatus,
                    'new_status' => 'failed',
                    'reason' => $request->reason,
                    'admin_id' => Auth::id()
                ]);

                return redirect()->route('admin.pembayaran.show', $pembayaran->id)
                    ->with('success', 'Pembayaran berhasil ditolak. Status tagihan dikembalikan ke belum bayar.');
            });

        } catch (\Exception $e) {
            Log::error('Error rejecting payment', [
                'message' => $e->getMessage(),
                'pembayaran_id' => $id,
                'admin_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal menolak pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Get statistics for dashboard
     */
    private function getStatistics($request = null)
    {
        $query = Pembayaran::query();

        // Apply same filters as main query if provided
        if ($request) {
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('tanggal_pembayaran', '>=', $request->tanggal_dari);
            }
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal_pembayaran', '<=', $request->tanggal_sampai);
            }
        }

        return [
            'total_pembayaran' => (clone $query)->count(),
            'pembayaran_hari_ini' => (clone $query)->whereDate('tanggal_pembayaran', today())->count(),
            'pembayaran_bulan_ini' => (clone $query)->whereMonth('tanggal_pembayaran', now()->month)
                                                    ->whereYear('tanggal_pembayaran', now()->year)
                                                    ->count(),
            'total_paid' => (clone $query)->where('status', 'lunas')->count(),
            'total_pending' => (clone $query)->where('status', 'pending')->count(),
            'total_failed' => (clone $query)->where('status', 'failed')->count(),
            'total_amount_paid' => (clone $query)->where('status', 'lunas')->sum('jumlah_bayar'),
            'total_amount_pending' => (clone $query)->where('status', 'pending')->sum('jumlah_bayar'),
            'unverified_count' => (clone $query)->where('status', 'lunas')
                                            ->where('is_verified', false)
                                            ->count(),
        ];
    }

    /**
     * Show confirm payment form
     */
    public function showConfirmForm($id)
    {
        $pembayaran = Pembayaran::with([
            'tagihan.meteran.user',
            'tagihan.pembacaanMeteran.meteran.user',
            'verifiedBy'
        ])->findOrFail($id);

        // Check if already verified
        if ($pembayaran->is_verified) {
            return redirect()->route('admin.pembayaran.show', $pembayaran->id)
                ->with('info', 'Pembayaran ini sudah diverifikasi.');
        }

        // Check if payment can be confirmed
        if (!in_array($pembayaran->status, ['lunas', 'pending'])) {
            return redirect()->route('admin.pembayaran.show', $pembayaran->id)
                ->with('error', 'Pembayaran dengan status "' . $pembayaran->status . '" tidak dapat dikonfirmasi.');
        }

        return view('pages.admin.pembayaran.confirm', compact('pembayaran'));
    }

    /**
     * Show reject payment form
     */
    public function showRejectForm($id)
    {
        $pembayaran = Pembayaran::with([
            'tagihan.meteran.user',
            'tagihan.pembacaanMeteran.meteran.user',
            'verifiedBy'
        ])->findOrFail($id);

        // Check if already verified
        if ($pembayaran->is_verified) {
            return redirect()->route('admin.pembayaran.show', $pembayaran->id)
                ->with('error', 'Pembayaran yang sudah diverifikasi tidak dapat ditolak.');
        }

        // Check if payment can be rejected
        if ($pembayaran->status === 'failed') {
            return redirect()->route('admin.pembayaran.show', $pembayaran->id)
                ->with('info', 'Pembayaran ini sudah ditolak sebelumnya.');
        }

        return view('pages.admin.pembayaran.reject', compact('pembayaran'));
    }

    private function updateTagihanStatusSafely($tagihan, $newStatus, $pembayaranId, $maxAttempts = 5)
    {
        if (!$tagihan) {
            Log::warning('Tagihan not found for status update', [
                'pembayaran_id' => $pembayaranId,
                'target_status' => $newStatus
            ]);
            return false;
        }

        $attempts = 0;
        $originalStatus = $tagihan->status;
        
        while ($attempts < $maxAttempts) {
            try {
                // Refresh tagihan data
                $tagihan->refresh();
                
                // Update status
                $tagihan->status = $newStatus;
                $tagihan->updated_at = now();
                
                // Force save with timestamp update
                $result = $tagihan->save();
                
                if (!$result) {
                    throw new \Exception('Save method returned false');
                }
                
                // Verify the update by refreshing and checking
                $tagihan->refresh();
                
                if ($tagihan->status === $newStatus) {
                    Log::info('Tagihan status updated successfully', [
                        'tagihan_id' => $tagihan->id,
                        'pembayaran_id' => $pembayaranId,
                        'original_status' => $originalStatus,
                        'new_status' => $newStatus,
                        'attempts' => $attempts + 1,
                        'final_verification' => true
                    ]);
                    return true;
                }
                
                // If status didn't change, log and retry
                Log::warning('Tagihan status update failed verification', [
                    'tagihan_id' => $tagihan->id,
                    'pembayaran_id' => $pembayaranId,
                    'expected_status' => $newStatus,
                    'actual_status' => $tagihan->status,
                    'attempt' => $attempts + 1
                ]);
                
                $attempts++;
                
                // Wait before retry (exponential backoff)
                if ($attempts < $maxAttempts) {
                    usleep(100000 * $attempts); // 0.1s, 0.2s, 0.3s, 0.4s
                }
                
            } catch (\Exception $e) {
                $attempts++;
                
                Log::error('Exception during tagihan status update', [
                    'tagihan_id' => $tagihan->id,
                    'pembayaran_id' => $pembayaranId,
                    'target_status' => $newStatus,
                    'attempt' => $attempts,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                if ($attempts < $maxAttempts) {
                    usleep(100000 * $attempts); // Wait before retry
                }
            }
        }
        
        // Final attempt with raw DB update
        try {
            Log::warning('Attempting raw database update for tagihan status', [
                'tagihan_id' => $tagihan->id,
                'target_status' => $newStatus
            ]);
            
            $affectedRows = DB::table('tagihan')
                ->where('id', $tagihan->id)
                ->update([
                    'status' => $newStatus,
                    'updated_at' => now()
                ]);
            
            if ($affectedRows > 0) {
                // Verify the raw update
                $updatedTagihan = DB::table('tagihan')
                    ->where('id', $tagihan->id)
                    ->first();
                
                if ($updatedTagihan && $updatedTagihan->status === $newStatus) {
                    Log::info('Tagihan status updated via raw DB query', [
                        'tagihan_id' => $tagihan->id,
                        'new_status' => $newStatus,
                        'affected_rows' => $affectedRows
                    ]);
                    
                    // Refresh the model
                    $tagihan->refresh();
                    return true;
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Raw database update also failed', [
                'tagihan_id' => $tagihan->id,
                'error' => $e->getMessage()
            ]);
        }
        
        // If all attempts failed
        Log::error('All attempts to update tagihan status failed', [
            'tagihan_id' => $tagihan->id,
            'pembayaran_id' => $pembayaranId,
            'target_status' => $newStatus,
            'total_attempts' => $maxAttempts,
            'final_status' => $tagihan->status
        ]);
        
        return false;
    }

    public function forceSyncStatus($id)
        {
        try {
            return DB::transaction(function () use ($id) {
                // $id bisa berupa pembayaran_id atau tagihan_id
                $pembayaran = Pembayaran::lockForUpdate()->find($id);
                
                if (!$pembayaran) {
                    // Coba cari tagihan dengan ID tersebut
                    $tagihan = Tagihan::lockForUpdate()->find($id);
                    
                    if (!$tagihan) {
                        return redirect()->back()
                            ->with('error', 'Pembayaran atau tagihan tidak ditemukan.');
                    }
                    
                    $pembayaran = $tagihan->pembayaran;
                    
                    if (!$pembayaran) {
                        // Tagihan tanpa pembayaran - set status berdasarkan jatuh tempo
                        $expectedStatus = ($tagihan->tanggal_jatuh_tempo && \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->isPast()) 
                            ? Tagihan::STATUS_TERLAMBAT 
                            : Tagihan::STATUS_BELUM_BAYAR;
                        
                        $tagihan->update([
                            'status' => $expectedStatus,
                            'is_manual_status' => false,
                            'manual_updated_by' => null,
                            'manual_updated_at' => null
                        ]);
                        
                        Log::info('Force sync: Updated tagihan without payment', [
                            'tagihan_id' => $tagihan->id,
                            'new_status' => $expectedStatus
                        ]);
                        
                        return redirect()->back()
                            ->with('success', 'Status tagihan berhasil disinkronkan (tanpa pembayaran).');
                    }
                }
                
                $tagihan = Tagihan::lockForUpdate()->find($pembayaran->tagihan_id);
                
                if (!$tagihan) {
                    return redirect()->back()
                        ->with('error', 'Tagihan terkait tidak ditemukan.');
                }
                
                $oldTagihanStatus = $tagihan->status;
                $expectedTagihanStatus = 'belum_bayar';
                
                // Tentukan status tagihan berdasarkan status pembayaran
                if ($pembayaran->status === 'lunas' && $pembayaran->is_verified) {
                    $expectedTagihanStatus = Tagihan::STATUS_SUDAH_BAYAR;
                } elseif ($pembayaran->status === 'pending') {
                    $expectedTagihanStatus = Tagihan::STATUS_MENUNGGU_KONFIRMASI;
                } elseif (in_array($pembayaran->status, ['failed', 'expired', 'cancelled'])) {
                    $isOverdue = $tagihan->tanggal_jatuh_tempo && \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->isPast();
                    $expectedTagihanStatus = $isOverdue ? Tagihan::STATUS_TERLAMBAT : Tagihan::STATUS_BELUM_BAYAR;
                }
                
                // Update tagihan status dan reset manual status
                $tagihan->update([
                    'status' => $expectedTagihanStatus,
                    'is_manual_status' => false,
                    'manual_updated_by' => null,
                    'manual_updated_at' => null
                ]);
                
                Log::info('Force sync status completed', [
                    'pembayaran_id' => $pembayaran->id,
                    'tagihan_id' => $tagihan->id,
                    'old_tagihan_status' => $oldTagihanStatus,
                    'new_tagihan_status' => $expectedTagihanStatus,
                    'pembayaran_status' => $pembayaran->status,
                    'is_verified' => $pembayaran->is_verified,
                    'admin_id' => Auth::id()
                ]);
                
                return redirect()->back()
                    ->with('success', 'Status berhasil disinkronkan paksa. Status tagihan: ' . ucfirst(str_replace('_', ' ', $expectedTagihanStatus)));
            });
            
        } catch (\Exception $e) {
            Log::error('Error in force sync status', [
                'id' => $id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);
            
            return redirect()->back()
                ->with('error', 'Gagal melakukan sinkronisasi paksa: ' . $e->getMessage());
        }
    }
    private function forceUpdateTagihanStatus($tagihanId, $newStatus)
    {
        try {
            Log::info('Attempting force update of tagihan status', [
                'tagihan_id' => $tagihanId,
                'target_status' => $newStatus
            ]);
            
            $affectedRows = DB::table('tagihan')
                ->where('id', $tagihanId)
                ->update([
                    'status' => $newStatus,
                    'updated_at' => now(),
                    'updated_by' => 'force_update_' . Auth::id()
                ]);
            
            if ($affectedRows > 0) {
                // Verify the update
                $updatedTagihan = DB::table('tagihan')
                    ->where('id', $tagihanId)
                    ->first();
                
                if ($updatedTagihan && $updatedTagihan->status === $newStatus) {
                    Log::info('Force update tagihan status successful', [
                        'tagihan_id' => $tagihanId,
                        'new_status' => $newStatus,
                        'affected_rows' => $affectedRows
                    ]);
                    return true;
                }
            }
            
            Log::error('Force update failed - no rows affected or verification failed', [
                'tagihan_id' => $tagihanId,
                'target_status' => $newStatus,
                'affected_rows' => $affectedRows
            ]);
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Exception during force update tagihan status', [
                'tagihan_id' => $tagihanId,
                'target_status' => $newStatus,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    private function scheduleTagihanStatusCheck($pembayaranId, $tagihanId)
    {
        try {
            // Store in a queue table or cache for background processing
            DB::table('status_sync_queue')->insert([
                'pembayaran_id' => $pembayaranId,
                'tagihan_id' => $tagihanId,
                'expected_status' => 'sudah_bayar',
                'attempts' => 0,
                'created_at' => now(),
                'process_after' => now()->addMinutes(2) // Process after 2 minutes
            ]);
            
            Log::info('Scheduled tagihan status check', [
                'pembayaran_id' => $pembayaranId,
                'tagihan_id' => $tagihanId
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to schedule tagihan status check', [
                'pembayaran_id' => $pembayaranId,
                'tagihan_id' => $tagihanId,
                'error' => $e->getMessage()
            ]);
        }
    }
    private function createStatusAuditLog($data)
    {
        try {
            DB::table('status_audit_log')->insert([
                'pembayaran_id' => $data['pembayaran_id'],
                'tagihan_id' => $data['tagihan_id'],
                'action' => $data['action'],
                'old_pembayaran_status' => $data['old_pembayaran_status'],
                'new_pembayaran_status' => $data['new_pembayaran_status'],
                'old_tagihan_status' => $data['old_tagihan_status'],
                'expected_tagihan_status' => $data['expected_tagihan_status'],
                'tagihan_update_success' => $data['tagihan_update_success'],
                'admin_id' => $data['admin_id'],
                'admin_note' => $data['admin_note'],
                'created_at' => $data['timestamp']
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create audit log', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
        }
    }
    public function checkStatusConsistency($id)
        {
        try {
            // $id bisa berupa tagihan_id atau pembayaran_id
            // Coba cari pembayaran dulu berdasarkan ID
            $pembayaran = Pembayaran::with('tagihan')->find($id);
            
            if (!$pembayaran) {
                // Jika tidak ada pembayaran dengan ID tersebut, coba cari tagihan dengan ID tersebut
                $tagihan = Tagihan::with('pembayaran')->find($id);
                
                if (!$tagihan) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Tagihan atau pembayaran tidak ditemukan'
                    ], 404);
                }
                
                $pembayaran = $tagihan->pembayaran;
                
                // Jika tagihan tidak memiliki pembayaran
                if (!$pembayaran) {
                    $expectedStatus = ($tagihan->tanggal_jatuh_tempo && \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->isPast()) 
                        ? 'terlambat' 
                        : 'belum_bayar';
                    
                    return response()->json([
                        'status' => 'success',
                        'data' => [
                            'tagihan_id' => $tagihan->id,
                            'pembayaran_id' => null,
                            'current_tagihan_status' => $tagihan->status,
                            'expected_tagihan_status' => $expectedStatus,
                            'pembayaran_status' => 'No payment',
                            'is_verified' => false,
                            'is_consistent' => $tagihan->status === $expectedStatus,
                            'last_checked' => now()->toISOString(),
                            'is_manual_status' => $tagihan->is_manual_status ?? false
                        ]
                    ]);
                }
            }
            
            $tagihan = $pembayaran->tagihan;
            
            if (!$tagihan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tagihan terkait tidak ditemukan'
                ], 404);
            }
            
            // Tentukan status tagihan yang diharapkan berdasarkan status pembayaran
            $expectedTagihanStatus = 'belum_bayar'; // default
            
            if ($pembayaran->status === 'lunas' && $pembayaran->is_verified) {
                $expectedTagihanStatus = 'sudah_bayar';
            } elseif ($pembayaran->status === 'pending') {
                $expectedTagihanStatus = 'menunggu_konfirmasi';
            } elseif (in_array($pembayaran->status, ['failed', 'expired', 'cancelled'])) {
                // Cek apakah terlambat berdasarkan tanggal jatuh tempo
                $isOverdue = $tagihan->tanggal_jatuh_tempo && \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->isPast();
                $expectedTagihanStatus = $isOverdue ? 'terlambat' : 'belum_bayar';
            }
            
            $isConsistent = ($tagihan->status === $expectedTagihanStatus);
            
            // Jika manual status, konsistensi mungkin tidak penting
            if ($tagihan->is_manual_status ?? false) {
                $isConsistent = true; // Manual status dianggap konsisten
            }
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'pembayaran_id' => $pembayaran->id,
                    'tagihan_id' => $tagihan->id,
                    'pembayaran_status' => $pembayaran->status,
                    'is_verified' => $pembayaran->is_verified,
                    'current_tagihan_status' => $tagihan->status,
                    'expected_tagihan_status' => $expectedTagihanStatus,
                    'is_consistent' => $isConsistent,
                    'is_manual_status' => $tagihan->is_manual_status ?? false,
                    'manual_updated_at' => $tagihan->manual_updated_at ?? null,
                    'last_checked' => now()->toISOString()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error checking status consistency', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal melakukan pengecekan konsistensi: ' . $e->getMessage()
            ], 500);
        }
    }
}