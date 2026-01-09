<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PembacaanMeteran;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Mapping bulan Indonesia
        $bulanIndonesia = [
            1 => 'Januari',
            2 => 'Februari', 
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        
        $bulanSekarang = $bulanIndonesia[now()->month];
        $tahunSekarang = now()->year;
        
        // Ambil tagihan bulan ini berdasarkan bulan dan tahun yang tepat
        $tagihanBulanIni = Tagihan::where('user_id', $user->id)
            ->where('bulan', $bulanSekarang)
            ->where('tahun', $tahunSekarang)
            ->first();

        // Jika tidak ada tagihan bulan ini, ambil tagihan terbaru
        if (!$tagihanBulanIni) {
            $tagihanBulanIni = Tagihan::where('user_id', $user->id)
                ->latest('created_at')
                ->first();
        }

        // Penggunaan bulan ini dari data tagihan
        $penggunaanBulanIni = $tagihanBulanIni ? $tagihanBulanIni->pemakaian : 0;

        // Status pembayaran berdasarkan status tagihan
        $statusPembayaran = 'Belum Lunas';
        if ($tagihanBulanIni) {
            if ($tagihanBulanIni->status == 'sudah_bayar') {
                $statusPembayaran = 'Lunas';
            } elseif ($tagihanBulanIni->status == 'terlambat') {
                $statusPembayaran = 'Terlambat';
            } elseif ($tagihanBulanIni->status == 'menunggu_konfirmasi') {
                $statusPembayaran = 'Menunggu Konfirmasi';
            } elseif ($tagihanBulanIni->status == 'belum_bayar') {
                $statusPembayaran = 'Belum Lunas';
            }
        }

        // Riwayat pembayaran berdasarkan data tagihan dengan urutan yang benar
        $riwayatPembayaran = Tagihan::where('user_id', $user->id)
            ->orderBy('tahun', 'desc')
            ->orderByRaw("CASE 
                WHEN bulan = 'Desember' THEN 12
                WHEN bulan = 'November' THEN 11
                WHEN bulan = 'Oktober' THEN 10
                WHEN bulan = 'September' THEN 9
                WHEN bulan = 'Agustus' THEN 8
                WHEN bulan = 'Juli' THEN 7
                WHEN bulan = 'Juni' THEN 6
                WHEN bulan = 'Mei' THEN 5
                WHEN bulan = 'April' THEN 4
                WHEN bulan = 'Maret' THEN 3
                WHEN bulan = 'Februari' THEN 2
                WHEN bulan = 'Januari' THEN 1
                ELSE 0 END DESC")
            ->get()
            ->map(function ($tagihan) {
                return (object) [
                    'periode' => $tagihan->bulan . ' ' . $tagihan->tahun,
                    'penggunaan' => $tagihan->pemakaian,
                    'tagihan' => $tagihan->total_tagihan,
                    'status' => $this->getStatusText($tagihan->status),
                    'tanggal_bayar' => $tagihan->tanggal_bayar ? 
                        Carbon::parse($tagihan->tanggal_bayar)->format('d/m/Y') : 
                        ($tagihan->status == 'sudah_bayar' && $tagihan->updated_at ? 
                         Carbon::parse($tagihan->updated_at)->format('d/m/Y') : null)
                ];
            });

        return view('pages.pelanggan.dashboard.index', compact(
            'penggunaanBulanIni',
            'tagihanBulanIni',
            'statusPembayaran',
            'riwayatPembayaran'
        ));
    }

    /**
     * Convert status code to readable text
     */
    private function getStatusText($status)
    {
        switch ($status) {
            case 'sudah_bayar':
                return 'Lunas';
            case 'belum_bayar':
                return 'Belum Lunas';
            case 'terlambat':
                return 'Terlambat';
            case 'menunggu_konfirmasi':
                return 'Menunggu Konfirmasi';
            default:
                return 'Belum Lunas';
        }
    }
}