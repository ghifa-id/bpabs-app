<?php

namespace App\Events;

use App\Models\Pembayaran;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PembayaranStatusUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pembayaran;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new event instance.
     *
     * @param Pembayaran $pembayaran
     * @param string $oldStatus
     * @param string $newStatus
     */
    public function __construct(Pembayaran $pembayaran, string $oldStatus, string $newStatus)
    {
        $this->pembayaran = $pembayaran;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}