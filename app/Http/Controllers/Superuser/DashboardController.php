<?php

namespace App\Http\Controllers\Superuser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tagihan;
use App\Models\Pembayaran;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalTagihan = Tagihan::count();
        
        $totalPembayaran = Pembayaran::count();
        
        $recentPembayaran = Pembayaran::with('tagihan')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('pages.superuser.dashboard.index', compact(
            'totalUsers',
            'totalTagihan',
            'totalPembayaran',
            'recentPembayaran'
        ));
    }
}