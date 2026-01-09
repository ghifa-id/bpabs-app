<?php

namespace App\Http\Controllers\Superuser;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Tarif;
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
        $tagihan = Tagihan::with(['user', 'meteran'])->latest()->paginate(10);
        
        return view('pages.superuser.tagihan.index', compact('tagihan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tarif = Tarif::all();
        
        return view('pages.superuser.tagihan.create', compact('tarif'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'meteran_id' => 'required|exists:meteran,id',
            'bulan' => 'required|string|max:20',
            'tahun' => 'required|integer|min:2020|max:2030',
            'meter_awal' => 'required|numeric|min:0',
            'meter_akhir' => 'required|numeric|min:0',
            'tarif_per_m3' => 'required|numeric|min:0',
            'tanggal_tagihan' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after:today',
        ], [
            'user_id.required' => 'Pelanggan harus dipilih',
            'meteran_id.required' => 'Meteran harus dipilih',
            'meter_akhir.min' => 'Meter akhir tidak boleh negatif',
            'tanggal_tagihan.required' => 'Tanggal tagihan harus diisi',
            'tanggal_tagihan.date' => 'Format tanggal tagihan tidak valid',
            'tanggal_jatuh_tempo.after' => 'Tanggal jatuh tempo harus setelah hari ini',
            'tarif_per_m3.required' => 'Tarif per m³ harus dipilih',
        ]);

        try {
            Log::info('Creating tagihan with data:', $request->all());

            // Validasi meter akhir harus lebih besar dari meter awal
            if ($request->meter_akhir <= $request->meter_awal) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Meter akhir harus lebih besar dari meter awal.');
            }

            // Cek apakah tagihan untuk periode ini sudah ada
            $existingTagihan = Tagihan::where('user_id', $request->user_id)
                ->where('meteran_id', $request->meteran_id)
                ->where('bulan', $request->bulan)
                ->where('tahun', $request->tahun)
                ->first();

            if ($existingTagihan) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Tagihan untuk periode ' . $request->bulan . ' ' . $request->tahun . ' sudah ada.');
            }

            // Hitung pemakaian dan biaya
            $pemakaian = $request->meter_akhir - $request->meter_awal;
            $biayaPemakaian = $pemakaian * $request->tarif_per_m3;
            $biayaAdmin = 5000;
            $totalTagihan = $biayaPemakaian + $biayaAdmin;

            // Generate nomor tagihan unik
            $nomorTagihan = 'TGH-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Format tanggal
            $tanggalTagihan = Carbon::parse($request->tanggal_tagihan)->format('Y-m-d H:i:s');
            $tanggalJatuhTempo = Carbon::parse($request->tanggal_jatuh_tempo)->format('Y-m-d');

            // Data sesuai dengan struktur migration
            $tagihanData = [
                'user_id' => $request->user_id,
                'meteran_id' => $request->meteran_id,
                'pembacaan_id' => null, // nullable
                'pembayaran_id' => null, // nullable
                'nomor_tagihan' => $nomorTagihan,
                'tanggal_tagihan' => $tanggalTagihan,
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'meter_awal' => $request->meter_awal,
                'meter_akhir' => $request->meter_akhir,
                'jumlah_pemakaian' => $pemakaian,
                'tarif_per_kubik' => $request->tarif_per_m3,
                'biaya_pemakaian' => $biayaPemakaian,
                'biaya_admin' => $biayaAdmin,
                'biaya_beban' => 0,
                'denda' => 0,
                'total_tagihan' => $totalTagihan,
                'status' => 'belum_bayar',
                'keterangan' => 'Tagihan dibuat oleh admin: ' . (Auth::user()->nama ?? Auth::user()->name),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            Log::info('Final tagihan data:', $tagihanData);

            // Create tagihan menggunakan Model
            $tagihan = Tagihan::create($tagihanData);

            Log::info('Tagihan created successfully', [
                'tagihan_id' => $tagihan->id,
                'nomor_tagihan' => $nomorTagihan,
                'user_id' => $request->user_id,
                'total' => $totalTagihan
            ]);

            return redirect()->route('superuser.tagihan.index')
                ->with('success', 'Tagihan berhasil dibuat. Nomor: ' . $nomorTagihan . ', Total: Rp ' . number_format($totalTagihan, 0, ',', '.'));

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error creating tagihan', [
                'error' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'request_data' => $request->all()
            ]);

            $errorMsg = $e->getMessage();
            $errorMessage = 'Gagal membuat tagihan karena kesalahan database.';
            
            if (preg_match("/Unknown column '([^']+)'/", $errorMsg, $matches)) {
                $columnName = $matches[1];
                $errorMessage = "Kolom '$columnName' tidak ditemukan di tabel tagihan. Periksa struktur database.";
            } elseif (str_contains($errorMsg, 'cannot be null')) {
                preg_match("/Column '([^']+)' cannot be null/", $errorMsg, $matches);
                $columnName = $matches[1] ?? 'unknown';
                $errorMessage = "Kolom '$columnName' tidak boleh kosong di database.";
            } elseif (str_contains($errorMsg, 'Duplicate entry')) {
                $errorMessage = 'Tagihan dengan data yang sama sudah ada.';
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);

        } catch (\Exception $e) {
            Log::error('Failed to create tagihan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat tagihan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Tagihan $tagihan)
    {
        $tagihan->load(['user', 'meteran']);
        
        return view('pages.superuser.tagihan.show', compact('tagihan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tagihan $tagihan)
    {
        $tarif = Tarif::all();
        
        return view('pages.superuser.tagihan.edit', compact('tagihan', 'tarif'));
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

        if ($request->has('status')) {
            $updateData['status'] = $request->status;
        }

        $tagihan->update($updateData);

        return redirect()->route('superuser.tagihan.index')
                        ->with('success', 'Tagihan berhasil diperbarui.');
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Tagihan $tagihan)
    {
        $tagihan->load(['user', 'meteran']);
        return view('pages.superuser.tagihan.delete', compact('tagihan'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tagihan $tagihan)
    {
        $tagihan->delete();
        
        return redirect()->route('superuser.tagihan.index')
                        ->with('success', 'Tagihan berhasil dihapus.');
    }
}