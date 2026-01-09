<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PembacaanMeteran;
use App\Models\Tagihan;
use App\Models\Meteran;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Validasi role - pastikan user adalah petugas
        if (!$user->isPetugas()) {
            abort(403, 'Akses ditolak. Hanya petugas yang dapat mengakses dashboard ini.');
        }
        
        // Statistik Personal Petugas yang Login
        $pembacaanPersonalHariIni = PembacaanMeteran::whereDate('tanggal_meteran', Carbon::today())
            ->where('petugas_id', $user->id)
            ->count();
            
        $pembacaanPersonalBulanIni = PembacaanMeteran::whereMonth('tanggal_meteran', Carbon::now()->month)
            ->whereYear('tanggal_meteran', Carbon::now()->year)
            ->where('petugas_id', $user->id)
            ->count();
        
        // Statistik Global (Semua Petugas)
        $pembacaanHariIni = PembacaanMeteran::whereDate('tanggal_meteran', Carbon::today())
            ->count();
        
        $pembacaanBulanIni = PembacaanMeteran::whereMonth('tanggal_meteran', Carbon::now()->month)
            ->whereYear('tanggal_meteran', Carbon::now()->year)
            ->count();
        
        // Total Meteran Aktif yang Perlu Dibaca
        $totalMeteranAktif = Meteran::where('status', 'aktif')->count();
        
        // Meteran yang Belum Dibaca Bulan Ini
        $meteranSudahBaca = PembacaanMeteran::whereMonth('tanggal_meteran', Carbon::now()->month)
            ->whereYear('tanggal_meteran', Carbon::now()->year)
            ->distinct('id_meteran')
            ->count('id_meteran');
            
        $meteranBelumBaca = $totalMeteranAktif - $meteranSudahBaca;
        
        // Persentase Progress Pembacaan Bulan Ini
        $progressPembacaan = $totalMeteranAktif > 0 ? 
            round(($meteranSudahBaca / $totalMeteranAktif) * 100, 1) : 0;
        
        // Riwayat Pembacaan Terbaru Personal (10 terakhir)
        $riwayatPembacaan = PembacaanMeteran::with(['meteran.user'])
            ->where('petugas_id', $user->id)
            ->orderBy('tanggal_meteran', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($pembacaan) {
                return (object) [
                    'tanggal' => $pembacaan->tanggal_meteran->format('d/m/Y'),
                    'waktu' => $pembacaan->tanggal_meteran->format('H:i'),
                    'nomor_meteran' => $pembacaan->meteran->nomor_meteran ?? 'N/A',
                    'pelanggan' => $pembacaan->meteran->user->name ?? 'N/A',
                    'meter_awal' => $pembacaan->meter_awal,
                    'meter_akhir' => $pembacaan->meter_akhir,
                    'pemakaian' => $pembacaan->meter_akhir - $pembacaan->meter_awal,
                    'status' => $pembacaan->status,
                    'catatan' => $pembacaan->catatan
                ];
            });
        
        // Statistik Mingguan
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $pembacaanMingguIni = PembacaanMeteran::whereBetween('tanggal_meteran', [$startOfWeek, $endOfWeek])
            ->count();
        
        // Total Petugas Aktif (dari tabel users dengan role = 'petugas')
        $totalPetugas = User::where('role', 'petugas')
            ->where('status', 'aktif')
            ->count();
        
        // Target Pembacaan Harian (berdasarkan total meteran dibagi 30 hari)
        $targetHarian = $totalMeteranAktif > 0 ? ceil($totalMeteranAktif / 30) : 0;
        
        // Rata-rata pembacaan harian per petugas
        $rataRataPembacaanHarian = $totalPetugas > 0 ? 
            number_format($pembacaanHariIni / $totalPetugas, 1) : 0;
        
        // Status pencapaian target hari ini (global)
        $statusTarget = $pembacaanHariIni >= $targetHarian ? 'Tercapai' : 'Belum Tercapai';
        
        // Target Personal Petugas (berdasarkan pembagian meteran per petugas)
        $targetPersonalHarian = $totalPetugas > 0 ? ceil($totalMeteranAktif / $totalPetugas / 30) : 0;
        $targetPersonalBulanan = $totalPetugas > 0 ? ceil($totalMeteranAktif / $totalPetugas) : 0;
        
        // Status pencapaian target personal
        $statusTargetPersonal = $pembacaanPersonalHariIni >= $targetPersonalHarian ? 'Tercapai' : 'Belum Tercapai';
        
        // Persentase pencapaian personal bulan ini
        $persentasePersonalBulanan = $targetPersonalBulanan > 0 ? 
            round(($pembacaanPersonalBulanIni / $targetPersonalBulanan) * 100, 1) : 0;
        
        // Statistik Pembacaan Personal (jika petugas memiliki ID di tabel pembacaan)
        // Karena model tidak memiliki petugas_id, kita bisa menambahkan logika lain
        // atau menggunakan statistik global untuk semua petugas
        
        // Tambahan: Statistik bulanan untuk grafik/trend
        $pembacaanBulananTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = Carbon::now()->subMonths($i);
            $pembacaanBulananTrend[] = [
                'bulan' => $bulan->format('M Y'),
                'jumlah' => PembacaanMeteran::whereMonth('tanggal_meteran', $bulan->month)
                    ->whereYear('tanggal_meteran', $bulan->year)
                    ->count()
            ];
        }
        
        // Statistik Status Pembacaan
        $statusPembacaan = [
            'selesai' => PembacaanMeteran::where('status', 'selesai')
                ->whereMonth('tanggal_meteran', Carbon::now()->month)
                ->whereYear('tanggal_meteran', Carbon::now()->year)
                ->count(),
            'pending' => PembacaanMeteran::where('status', 'pending')
                ->whereMonth('tanggal_meteran', Carbon::now()->month)
                ->whereYear('tanggal_meteran', Carbon::now()->year)
                ->count(),
            'bermasalah' => PembacaanMeteran::where('status', 'bermasalah')
                ->whereMonth('tanggal_meteran', Carbon::now()->month)
                ->whereYear('tanggal_meteran', Carbon::now()->year)
                ->count()
        ];
        
        // Target dan Achievement
        $targetBulanan = $totalMeteranAktif; // Semua meteran harus dibaca dalam sebulan
        $achievementBulanan = $meteranSudahBaca;
        $persentaseAchievement = $targetBulanan > 0 ? 
            round(($achievementBulanan / $targetBulanan) * 100, 1) : 0;
        
        // Kinerja Tim
        $kinerjaTim = [
            'total_pembacaan_hari_ini' => $pembacaanHariIni,
            'total_pembacaan_bulan_ini' => $pembacaanBulanIni,
            'rata_rata_per_petugas' => $rataRataPembacaanHarian,
            'efisiensi' => $targetHarian > 0 ? round(($pembacaanHariIni / $targetHarian) * 100, 1) : 0
        ];
        
        return view('pages.petugas.dashboard.index', compact(
            // Statistik Global
            'pembacaanHariIni',
            'pembacaanBulanIni',
            'meteranBelumBaca',
            'progressPembacaan',
            'totalMeteranAktif',
            'targetHarian',
            'statusTarget',
            
            // Statistik Personal
            'pembacaanPersonalHariIni',
            'pembacaanPersonalBulanIni',
            'targetPersonalHarian',
            'targetPersonalBulanan',
            'statusTargetPersonal',
            'persentasePersonalBulanan',
            
            // Data Umum
            'riwayatPembacaan',
            'pembacaanMingguIni',
            'totalPetugas',
            'rataRataPembacaanHarian',
            'pembacaanBulananTrend',
            'statusPembacaan',
            'targetBulanan',
            'achievementBulanan',
            'persentaseAchievement',
            'kinerjaTim'
        ));
    }
    
    /**
     * Get personal statistics for logged-in petugas
     * Menggunakan relasi pembacaanMeteran dari model User
     */
    public function personalStats()
    {
        $user = Auth::user();
        
        // Validasi role
        if (!$user->isPetugas()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Statistik personal menggunakan relasi Eloquent
        $personalToday = $user->pembacaanMeteran()
            ->whereDate('tanggal_meteran', today())
            ->count();
            
        $personalMonth = $user->pembacaanMeteran()
            ->whereMonth('tanggal_meteran', now()->month)
            ->whereYear('tanggal_meteran', now()->year)
            ->count();
            
        // Target personal (estimasi berdasarkan total meteran dibagi jumlah petugas)
        $totalMeteran = Meteran::where('status', 'aktif')->count();
        $totalPetugas = User::byRole('petugas')->active()->count();
        $personalTarget = $totalPetugas > 0 ? ceil($totalMeteran / $totalPetugas / 30) : 0;
        
        return response()->json([
            'personal_today' => $personalToday,
            'personal_month' => $personalMonth,
            'personal_target' => $personalTarget,
            'achievement_percentage' => $personalTarget > 0 ? round(($personalToday / $personalTarget) * 100, 1) : 0
        ]);
    }
}