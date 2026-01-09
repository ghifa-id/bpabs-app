<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Tarif;
use App\Models\PembacaanMeteran;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TagihanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $tagihan = Tagihan::with(['user', 'meteran', 'pembayaran'])
                       ->latest()
                       ->paginate(10);
        
        return view('pages.admin.tagihan.index', compact('tagihan'));
    }
    
    private function sinkronkanStatusTagihan()
    {
        $tagihans = Tagihan::whereHas('pembayaran', function($q) {
                $q->where('status', Pembayaran::STATUS_PAID)
                  ->where('is_verified', true);
            })
            ->where('status', '!=', Tagihan::STATUS_SUDAH_BAYAR)
            ->where(function($query) {
                // Hanya sync yang bukan manual status atau sudah lewat 24 jam
                $query->where('is_manual_status', false)
                      ->orWhere('manual_updated_at', '<', now()->subHours(24))
                      ->orWhereNull('manual_updated_at');
            })
            ->chunk(200, function ($tagihans) {
                foreach ($tagihans as $tagihan) {
                    if ($tagihan->shouldAutoSync()) {
                        $tagihan->update(['status' => Tagihan::STATUS_SUDAH_BAYAR]);
                        
                        Log::info('Auto-synced tagihan status', [
                            'tagihan_id' => $tagihan->id,
                            'new_status' => Tagihan::STATUS_SUDAH_BAYAR
                        ]);
                    }
                }
            });
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tarif = Tarif::all();
        $pembacaans = PembacaanMeteran::with(['meteran.user'])
            ->whereDoesntHave('tagihan')
            ->latest()
            ->get()
            ->map(function ($pembacaan) {
                // Pastikan format bulan dan tahun konsisten
                $pembacaan->bulan = $pembacaan->bulan ?? Carbon::parse($pembacaan->created_at)->format('F');
                $pembacaan->tahun = $pembacaan->tahun ?? Carbon::parse($pembacaan->created_at)->format('Y');
                return $pembacaan;
            });

        return view('pages.admin.tagihan.create', compact('tarif', 'pembacaans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    $request->validate([
        'pembacaan_id' => 'required|exists:pembacaan_meteran,id',
        'tarif_per_m3' => 'required|numeric|min:0',
        'tanggal_tagihan' => 'required|date',
        'tanggal_jatuh_tempo' => 'required|date|after:today',
    ]);

    try {
        $pembacaan = PembacaanMeteran::findOrFail($request->pembacaan_id);
        $meteran = $pembacaan->meteran;

        // Cek apakah sudah ada tagihan untuk pembacaan ini
        if ($pembacaan->tagihan) {
            return back()->with('error', 'Sudah ada tagihan untuk pembacaan ini');
        }

        // Hitung biaya
        $pemakaian = $pembacaan->meter_akhir - $pembacaan->meter_awal;
        $biayaPemakaian = $pemakaian * $request->tarif_per_m3;
        $biayaAdmin = 5000;
        $totalTagihan = $biayaPemakaian + $biayaAdmin;

        $tagihan = Tagihan::create([
            'user_id' => $meteran->user_id,
            'meteran_id' => $meteran->id,
            'pembacaan_id' => $pembacaan->id,
            'tanggal_tagihan' => $request->tanggal_tagihan,
            'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
            'bulan' => $pembacaan->bulan,
            'tahun' => $pembacaan->tahun,
            'meter_awal' => $pembacaan->meter_awal,
            'meter_akhir' => $pembacaan->meter_akhir,
            'jumlah_pemakaian' => $pemakaian,
            'tarif_per_kubik' => $request->tarif_per_m3,
            'biaya_pemakaian' => $biayaPemakaian,
            'biaya_admin' => $biayaAdmin,
            'total_tagihan' => $totalTagihan,
            'status' => 'belum_bayar',
            'keterangan' => 'Tagihan dibuat berdasarkan pembacaan meteran',
        ]);

        return redirect()->route('admin.tagihan.index')
            ->with('success', 'Tagihan berhasil dibuat');
            
    } catch (\Exception $e) {
        return back()->with('error', 'Gagal membuat tagihan: ' . $e->getMessage());
    }
}

    /**
     * Display the specified resource.
     */
    public function show(Tagihan $tagihan)
    {
        $this->sinkronkanStatusTagihan();

        $tagihan->load([
        'user', 
        'meteran', 
        'pembayaran' // Load the payment relationship
        ]);

        return view('pages.admin.tagihan.show', compact('tagihan'));
    
    }
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tagihan $tagihan)
    {

        $tarif = Tarif::all();
        return view('pages.admin.tagihan.edit', compact('tagihan', 'tarif'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tagihan $tagihan)
    {
        $request->validate([
            'bulan' => 'required|string',
            'tahun' => 'required|integer',
            'meter_awal' => 'required|numeric',
            'meter_akhir' => 'required|numeric|gt:meter_awal',
            'tarif_per_m3' => 'required|numeric',
            'tanggal_jatuh_tempo' => 'required|date',
            'status' => 'sometimes|in:belum_bayar,sudah_bayar,terlambat',
        ]);

        try {
            return DB::transaction(function () use ($request, $tagihan) {
                $oldStatus = $tagihan->status;
                $newStatus = $request->status ?? $oldStatus;
                
                // Hitung ulang biaya
                $pemakaian = (float)$request->meter_akhir - (float)$request->meter_awal;
                $tarifPerKubik = (float)$request->tarif_per_m3;
                $biayaPemakaian = $pemakaian * $tarifPerKubik;
                $biayaAdmin = 5000;
                $totalTagihan = $biayaPemakaian + $biayaAdmin;

                $updateData = [
                    'bulan' => $request->bulan,
                    'tahun' => $request->tahun,
                    'meter_awal' => $request->meter_awal,
                    'meter_akhir' => $request->meter_akhir,
                    'jumlah_pemakaian' => $pemakaian,
                    'tarif_per_kubik' => $tarifPerKubik,
                    'biaya_pemakaian' => $biayaPemakaian,
                    'biaya_admin' => $biayaAdmin,
                    'total_tagihan' => $totalTagihan,
                    'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                ];

                // Update data dasar tagihan
                $tagihan->update($updateData);

                // Handle perubahan status secara manual
                if ($request->has('status') && $oldStatus !== $newStatus) {
                    $tagihan->updateStatusManually($newStatus, Auth::id(), true);
                }

                Log::info('Tagihan updated by admin', [
                    'tagihan_id' => $tagihan->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'is_manual_status_change' => $request->has('status'),
                    'admin_id' => Auth::id()
                ]);

                return redirect()->route('admin.tagihan.show', $tagihan)
                    ->with('success', 'Tagihan berhasil diperbarui.' . 
                           ($oldStatus !== $newStatus ? ' Status berhasil diubah menjadi ' . ucfirst(str_replace('_', ' ', $newStatus)) . '.' : ''));
            });

        } catch (\Exception $e) {
            Log::error('Error updating tagihan', [
                'message' => $e->getMessage(),
                'tagihan_id' => $tagihan->id,
                'admin_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal memperbarui tagihan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function delete(Tagihan $tagihan)
    {
        $tagihan ->load(['user', 'meteran']);
        return view('pages.admin.tagihan.delete', compact('tagihan'));
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tagihan $tagihan)
    {
        $tagihan->delete();
        
        return redirect()->route('admin.tagihan.index')
                        ->with('success', 'Tagihan berhasil dihapus.');
    }
    public function resetManualStatus($id)
    {
        try {
            $tagihan = Tagihan::findOrFail($id);
            
            $tagihan->update([
                'is_manual_status' => false,
                'manual_updated_by' => null,
                'manual_updated_at' => null,
            ]);

            // Setelah reset, jalankan auto sync
            $this->sinkronkanStatusTagihan();

            return redirect()->back()
                ->with('success', 'Status manual direset. Sistem akan otomatis menyesuaikan status berdasarkan pembayaran.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mereset status manual: ' . $e->getMessage());
        }
    }
}