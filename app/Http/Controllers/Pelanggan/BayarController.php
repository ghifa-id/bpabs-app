<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Services\MidtransService;
use App\Services\CustomMidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BayarController extends Controller
{
    protected $midtransService;
    protected $customMidtransService;

    public function __construct(MidtransService $midtransService, CustomMidtransService $customMidtransService)
    {
        $this->midtransService = $midtransService;
        $this->customMidtransService = $customMidtransService;
    }

    /**
     * Show payment form
     */
    public function bayar($id)
    {
        try {
            // Step 1: Cari tagihan
            $tagihan = Tagihan::with(['meteran.user', 'pembayaran'])
                ->whereHas('meteran', function($query) {
                    $query->where('user_id', Auth::id());
                })
                ->where('id', $id)
                ->firstOrFail();

            Log::info('Starting payment process', [
                'tagihan_id' => $tagihan->id,
                'user_id' => Auth::id(),
                'amount' => $tagihan->total_tagihan
            ]);

            // Step 2: Validasi tagihan
            if ($tagihan->status === 'sudah_bayar') {
                return redirect()->route('pelanggan.tagihan.index')
                    ->with('info', 'Tagihan ini sudah dibayar.');
            }

            // Step 3: Cek pembayaran pending
            $existingPayment = $tagihan->pembayaran()
                ->where('status', 'pending')
                ->first();

            if ($existingPayment) {
                return redirect()->route('pelanggan.tagihan.index')
                    ->with('info', 'Tagihan ini sedang dalam proses pembayaran.');
            }

            // Step 4: Prepare data
            $orderId = 'PAY-' . $tagihan->id . '-' . time() . '-' . rand(100, 999);
            $amount = (int) $tagihan->total_tagihan;
            $user = $tagihan->meteran->user;

            $transactionDetails = [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ];

            $customerDetails = [
                'first_name' => $user->nama ?? 'Customer',
                'last_name' => '',
                'email' => $user->email,
                'phone' => $user->no_hp ?? '',
            ];

            $itemDetails = [
                [
                    'id' => 'tagihan-' . $tagihan->id,
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => 'Tagihan Air - ' . $tagihan->bulan . ' ' . $tagihan->tahun,
                    'category' => 'Water Bill',
                ]
            ];

            // Step 5: Create Snap Token dengan fallback ke custom service
            $snapToken = null;
            $serviceUsed = 'unknown';

            try {
                // Coba dengan service biasa dulu
                $snapToken = $this->midtransService->createSnapToken(
                    $transactionDetails,
                    $customerDetails,
                    $itemDetails
                );
                $serviceUsed = 'standard';
                
            } catch (\Exception $e) {
                Log::warning('Standard Midtrans service failed, trying custom service', [
                    'error' => $e->getMessage(),
                    'order_id' => $orderId
                ]);

                try {
                    // Fallback ke custom service
                    $snapToken = $this->customMidtransService->createSnapToken(
                        $transactionDetails,
                        $customerDetails,
                        $itemDetails
                    );
                    $serviceUsed = 'custom';
                    
                } catch (\Exception $e2) {
                    Log::error('Both Midtrans services failed', [
                        'standard_error' => $e->getMessage(),
                        'custom_error' => $e2->getMessage(),
                        'order_id' => $orderId
                    ]);
                    throw new \Exception('Tidak dapat terhubung ke payment gateway. Silakan coba lagi nanti atau hubungi customer service.');
                }
            }

            if (!$snapToken) {
                throw new \Exception('Gagal membuat token pembayaran');
            }

            // Step 6: Save pembayaran record
            $pembayaran = new Pembayaran();
            $pembayaran->tagihan_id = $tagihan->id;
            $pembayaran->order_id = $orderId;
            $pembayaran->jumlah_bayar = $tagihan->total_tagihan;
            $pembayaran->metode_pembayaran = 'online_payment';
            $pembayaran->status = 'pending';
            $pembayaran->tanggal_pembayaran = now();
            $pembayaran->snap_token = $snapToken;
            $pembayaran->keterangan = 'Service: ' . $serviceUsed;
            $pembayaran->save();

            // Step 7: Update tagihan status
            $tagihan->status = 'menunggu_konfirmasi';
            $tagihan->save();

            Log::info('Payment token created successfully', [
                'tagihan_id' => $tagihan->id,
                'order_id' => $orderId,
                'service_used' => $serviceUsed,
                'token_length' => strlen($snapToken)
            ]);

            return view('pages.pelanggan.tagihan.bayar', [
                'tagihan' => $tagihan,
                'snapToken' => $snapToken,
                'orderId' => $orderId,
                'clientKey' => config('midtrans.client_key'),
                'isProduction' => config('midtrans.is_production'),
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('pelanggan.tagihan.index')
                ->with('error', 'Tagihan tidak ditemukan atau Anda tidak memiliki akses.');

        } catch (\Exception $e) {
            Log::error('Payment page error', [
                'tagihan_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('pelanggan.tagihan.index')
                ->with('error', 'Gagal memuat halaman pembayaran: ' . $e->getMessage());
        }
    }
    public function paymentCallback(Request $request)
    {
        $orderId = $request->query('order_id');
        $status = $request->query('transaction_status');

        // Validasi parameter
        if (!$orderId || !$status) {
            return view('pages.pelanggan.tagihan.callback', [
                'status' => 'failed'
            ]);
        }
        if ($status === 'settlement') {
        $pembayaran = Pembayaran::where('order_id', $orderId)->first();
        if ($pembayaran) {
            $pembayaran->status = 'lunas';
            $pembayaran->save();
            
            // Langsung update tagihan
            $tagihan = $pembayaran->tagihan;
            $tagihan->status = Tagihan::STATUS_SUDAH_BAYAR;
            $tagihan->save();
        }
    }

        return view('pages.pelanggan.tagihan.callback', [
            'status' => $status
        ]);
    }
}