<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Meteran;
use App\Models\PembacaanMeteran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeteranController extends Controller
{
    public function index()
    {
        $meterans = Meteran::where('status', 'aktif')
            ->with(['pembacaanMeteran' => function($query) {
                $query->whereMonth('tanggal_meteran', now()->month)
                    ->whereYear('tanggal_meteran', now()->year);
            }])
            ->paginate(10);

        return view('pages.petugas.meteran.index', compact('meterans'));
    }
    public function create()
    {
        return view('pages.petugas.meteran.create');
    }

    public function store(Request $request)
        {
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'nomor_meteran' => 'required|exists:meteran,nomor_meteran',
        'bulan' => 'required|string',
        'tahun' => 'required|integer',
        'meter_awal' => 'required|numeric|min:0',
        'meter_akhir' => 'required|numeric|gte:meter_awal',
    ]);

        $meteran = Meteran::where('nomor_meteran', $request->nomor_meteran)->first();

        if ($meteran->user_id != $request->user_id) {
            return back()->withErrors(['nomor_meteran' => 'Nomor meteran tidak sesuai dengan pelanggan yang dipilih'])->withInput();
        }

        $monthName = $request->bulan;
        $monthNumber = date('m', strtotime("1 $monthName 2000"));

        $existingPembacaan = PembacaanMeteran::where('id_meteran', $meteran->id)
            ->whereMonth('tanggal_meteran', $monthNumber)
            ->whereYear('tanggal_meteran', $request->tahun)
            ->exists();

        if ($existingPembacaan) {
            return back()->withErrors(['bulan' => 'Sudah ada pembacaan untuk periode ini'])->withInput();
        }

        $readingDate = date('Y-m-d', strtotime("1 $monthName {$request->tahun}"));

        PembacaanMeteran::create([
            'id_meteran' => $meteran->id,
            'petugas_id' => Auth::id(),
            'tanggal_meteran' => $readingDate,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'meter_awal' => $request->meter_awal,
            'meter_akhir' => $request->meter_akhir,
            'status' => 'selesai',
        ]);

        return redirect()->route('petugas.meteran.index')
            ->with('success', 'Pembacaan meteran berhasil disimpan');
    }
    public function show(Meteran $meteran)
    {
        $pembacaans = $meteran->pembacaanMeteran()
            ->with('petugas')
            ->select('id', 'id_meteran', 'petugas_id', 'tanggal_meteran', 'bulan', 'tahun', 'meter_awal', 'meter_akhir', 'status') // Pastikan semua kolom yang diperlukan di-select
            ->latest()
            ->paginate(10);

        return view('pages.petugas.meteran.show', compact('meteran', 'pembacaans'));
    }
}