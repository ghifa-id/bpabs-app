<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Notification;

class MidtransNotificationController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Handle Midtrans notification webhook
     */
    public function handle(Request $request)
    {
        try {
            Log::info('Midtrans notification received', [
                'payload' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            // Initialize Midtrans notification
            $notification = new Notification();

            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? 'accept';
            $paymentType = $notification->payment_type;
            $transactionId = $notification->transaction_id;

            Log::info('Notification details', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'payment_type' => $paymentType,
                'transaction_id' => $transactionId
            ]);

            // Find pembayaran record
            $pembayaran = Pembayaran::where('order_id', $orderId)->first();

            if (!$pembayaran) {
                Log::error('Pembayaran not found for notification', [
                    'order_id' => $orderId
                ]);
                return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
            }

            // Verify signature (optional but recommended)
            $signatureKey = hash('sha512', $orderId . $notification->status_code . $notification->gross_amount . config('midtrans.server_key'));
            
            if ($notification->signature_key !== $signatureKey) {
                Log::error('Invalid signature for notification', [
                    'order_id' => $orderId,
                    'expected' => $signatureKey,
                    'received' => $notification->signature_key
                ]);
                return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
            }

            // Update pembayaran based on transaction status
            $this->updatePaymentStatus($pembayaran, $notification);

            Log::info('Notification processed successfully', [
                'order_id' => $orderId,
                'new_status' => $pembayaran->status
            ]);

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Error processing Midtrans notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update payment status based on Midtrans notification
     */
    private function updatePaymentStatus($pembayaran, $notification)
    {
        $oldStatus = $pembayaran->status;
        
        // Update payment details
        $pembayaran->transaction_id = $notification->transaction_id;
        $pembayaran->payment_type = $notification->payment_type;
        $pembayaran->fraud_status = $notification->fraud_status ?? 'accept';

        // Update status based on transaction_status
        switch ($notification->transaction_status) {
            case 'capture':
                if ($notification->fraud_status == 'accept') {
                    $pembayaran->status = 'paid';
                    $pembayaran->tanggal_pembayaran = now();
                    $pembayaran->tagihan->status = 'sudah_bayar';
                    $pembayaran->tagihan->save();
                }
                break;

            case 'settlement':
                $pembayaran->status = 'paid';
                $pembayaran->tanggal_pembayaran = now();
                $pembayaran->tagihan->status = 'sudah_bayar';
                $pembayaran->tagihan->save();
                break;

            case 'pending':
                $pembayaran->status = 'pending';
                $pembayaran->tagihan->status = 'menunggu_konfirmasi';
                $pembayaran->tagihan->save();
                break;

            case 'deny':
            case 'expire':
            case 'cancel':
                $pembayaran->status = 'failed';
                $pembayaran->tagihan->status = 'belum_bayar';
                $pembayaran->tagihan->save();
                break;
        }

        $pembayaran->save();

        Log::info('Payment status updated via notification', [
            'order_id' => $pembayaran->order_id,
            'old_status' => $oldStatus,
            'new_status' => $pembayaran->status,
            'transaction_status' => $notification->transaction_status
        ]);
    }
}