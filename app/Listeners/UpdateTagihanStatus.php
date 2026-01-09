<?php

namespace App\Listeners;

use App\Events\PembayaranStatusUpdated;
use App\Models\Tagihan;
use App\Models\Pembayaran;

class UpdateTagihanStatus
{
    /**
     * Handle the event.
     *
     * @param PembayaranStatusUpdated $event
     * @return void
     */
    public function handle(PembayaranStatusUpdated $event)
    {
        $pembayaran = $event->pembayaran;
        $tagihan = $pembayaran->tagihan;

        if (!$tagihan) {
            return;
        }

        // Jika pembayaran menjadi PAID dan terverifikasi
        if ($event->newStatus === Pembayaran::STATUS_PAID && $pembayaran->is_verified) {
            $tagihan->status = Tagihan::STATUS_SUDAH_BAYAR;
            $tagihan->save();
            return;
        }

        // Jika pembayaran gagal/dibatalkan
        if (in_array($event->newStatus, [
            Pembayaran::STATUS_FAILED, 
            Pembayaran::STATUS_CANCELLED,
            Pembayaran::STATUS_EXPIRED
        ])) {
            // Cek apakah tagihan sudah lewat jatuh tempo
            $isOverdue = $tagihan->tanggal_jatuh_tempo && 
                         now()->gt($tagihan->tanggal_jatuh_tempo);

            $tagihan->status = $isOverdue ? 
                Tagihan::STATUS_TERLAMBAT : 
                Tagihan::STATUS_BELUM_BAYAR;
                
            $tagihan->save();
        }
    }
}