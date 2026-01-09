<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pembayaran;
use App\Models\Tagihan;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $statusPembayaran = 'aktif';
        $tanggalJatuhTempo = now()->addDays(15)->format('d F Y');
        

        $riwayatPembayaran = Pembayaran::with(['tagihan.pembacaanMeteran.meteran'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            

        $tagihanBulanIni = Tagihan::whereHas('pembacaanMeteran.meteran')
            ->whereMonth('tanggal_tagihan', now()->month)
            ->whereYear('tanggal_tagihan', now()->year)
            ->first();
            
        $statusPembayaran = $tagihanBulanIni ? $tagihanBulanIni->status : 'tidak_ada';
        

        $totalPelanggan = \App\Models\User::where('role', 'pelanggan')->count();
        

        $pembayaranBulanIni = Pembayaran::whereMonth('tanggal_pembayaran', now()->month)
            ->whereYear('tanggal_pembayaran', now()->year)
            ->count();
            

        $menungguPembayaran = Tagihan::where('status', 'belum_bayar')->count();
        

        $tagihanTerlambat = Tagihan::where('status', 'terlambat')
            ->orWhere(function($query) {
                $query->where('tanggal_tagihan', '<', now())
                      ->where('status', 'belum_bayar');
            })
            ->count();
            

        $pembayaranTerbaru = Pembayaran::with(['tagihan.meteran.user', 'tagihan.pembacaanMeteran.meteran.user'])
            ->orderBy('tanggal_pembayaran', 'desc')
            ->take(5)
            ->get();
        
        return view('pages.admin.dashboard.index', compact(
            'statusPembayaran',
            'tagihanBulanIni', 
            'tanggalJatuhTempo',
            'riwayatPembayaran',
            'totalPelanggan',
            'pembayaranBulanIni',
            'menungguPembayaran',
            'tagihanTerlambat',
            'pembayaranTerbaru'
        ));
    }
}