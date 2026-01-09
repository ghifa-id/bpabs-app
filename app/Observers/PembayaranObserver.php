<?php

namespace App\Observers;

use App\Models\Pembayaran;

class PembayaranObserver
{
    public function updated(Pembayaran $pembayaran)
    {
        if ($pembayaran->isDirty(['status', 'is_verified'])) {
            if ($pembayaran->tagihan) {
                $pembayaran->tagihan->updateStatus();
            }
        }
    }

    public function created(Pembayaran $pembayaran)
    {
        if ($pembayaran->tagihan) {
            $pembayaran->tagihan->updateStatus();
        }
    }

    public function deleted(Pembayaran $pembayaran)
    {
        if ($pembayaran->tagihan) {
            $pembayaran->tagihan->updateStatus();
        }
    }
}