<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\User;
use Carbon\Carbon;

class PembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil sample tagihan yang sudah ada
        $tagihans = Tagihan::with('meteran.user')->take(10)->get();
        
        if ($tagihans->isEmpty()) {
            $this->command->warn('No tagihan found. Please run TagihanSeeder first.');
            return;
        }

        $this->command->info('Creating sample pembayaran records...');

        foreach ($tagihans as $index => $tagihan) {
            // Skip beberapa tagihan untuk simulasi yang belum dibayar
            if ($index % 3 === 0) {
                continue;
            }

            $paymentDate = now()->subDays(rand(1, 30));
            $status = $this->getRandomStatus($index);
            $method = $this->getRandomPaymentMethod();
            
            $pembayaran = Pembayaran::create([
                // Basic info
                'tagihan_id' => $tagihan->id,
                'jumlah_bayar' => $tagihan->total_tagihan,
                'gross_amount' => $tagihan->total_tagihan,
                'tanggal_pembayaran' => $paymentDate,
                'transaction_time' => $paymentDate,
                
                // Status
                'status' => $status,
                'metode_pembayaran' => $method,
                'payment_type' => $this->getPaymentType($method),
                
                // Midtrans simulation
                'order_id' => 'PAY-' . $tagihan->id . '-' . time() . '-' . rand(100, 999),
                'transaction_id' => $status !== 'pending' ? 'TXN-' . time() . '-' . rand(1000, 9999) : null,
                'snap_token' => $status === 'pending' ? $this->generateFakeSnapToken() : null,
                
                // Bank info for VA payments
                'bank' => in_array($method, ['bank_transfer', 'virtual_account']) ? $this->getRandomBank() : null,
                'va_number' => $method === 'virtual_account' ? $this->generateVANumber() : null,
                
                // Processing info
                'processed_at' => $status !== 'pending' ? $paymentDate->addMinutes(rand(1, 60)) : null,
                'processed_by' => $this->getProcessedBy($status, $method),
                
                // Verification
                'is_verified' => $status === 'paid' ? (rand(1, 10) > 2) : false, // 80% verified if paid
                'verified_at' => $status === 'paid' && rand(1, 10) > 2 ? $paymentDate->addHours(rand(1, 24)) : null,
                'verified_by' => $status === 'paid' && rand(1, 10) > 2 ? $this->getRandomAdmin() : null,
                
                // Notes
                'keterangan' => $this->getKeterangan($status, $method),
                'catatan_admin' => $status === 'paid' ? 'Pembayaran terverifikasi otomatis' : null,
                'verification_note' => $status === 'paid' && rand(1, 10) > 8 ? 'Manual verification required' : null,
                
                // Fraud status for Midtrans payments
                'fraud_status' => in_array($method, ['online_payment', 'midtrans']) ? 'accept' : null,
                
                // Midtrans response simulation
                'midtrans_response' => $this->generateMidtransResponse($status, $method, $tagihan),
                
                // URLs
                'finish_redirect_url' => route('pelanggan.tagihan.index'),
                'pdf_url' => $status === 'paid' ? '/storage/receipts/receipt-' . time() . '.pdf' : null,
            ]);

            // Update tagihan status based on payment status
            if ($status === 'paid') {
                $tagihan->update(['status' => 'sudah_bayar']);
            } elseif ($status === 'pending') {
                $tagihan->update(['status' => 'menunggu_konfirmasi']);
            }

            $this->command->info("Created payment for tagihan ID {$tagihan->id} with status: {$status}");
        }

        $this->command->info('Pembayaran seeder completed!');
    }

    /**
     * Get random payment status
     */
    private function getRandomStatus($index)
    {
        $statuses = [
            'paid' => 60,      // 60% paid
            'pending' => 20,   // 20% pending  
            'failed' => 10,    // 10% failed
            'cancelled' => 5,  // 5% cancelled
            'expired' => 5,    // 5% expired
        ];

        $rand = rand(1, 100);
        $cumulative = 0;
        
        foreach ($statuses as $status => $percentage) {
            $cumulative += $percentage;
            if ($rand <= $cumulative) {
                return $status;
            }
        }
        
        return 'paid';
    }

    /**
     * Get random payment method
     */
    private function getRandomPaymentMethod()
    {
        $methods = [
            'online_payment',
            'midtrans', 
            'bank_transfer',
            'virtual_account',
            'manual',
            'credit_card',
            'ewallet'
        ];

        return $methods[array_rand($methods)];
    }

    /**
     * Get payment type based on method
     */
    private function getPaymentType($method)
    {
        $mapping = [
            'online_payment' => 'bank_transfer',
            'midtrans' => 'bank_transfer',
            'bank_transfer' => 'bank_transfer',
            'virtual_account' => 'bank_transfer',
            'manual' => null,
            'credit_card' => 'credit_card',
            'ewallet' => 'gopay'
        ];

        return $mapping[$method] ?? null;
    }

    /**
     * Get random bank
     */
    private function getRandomBank()
    {
        $banks = ['bca', 'bni', 'bri', 'mandiri', 'permata', 'cimb'];
        return $banks[array_rand($banks)];
    }

    /**
     * Generate fake VA number
     */
    private function generateVANumber()
    {
        return '8808' . str_pad(rand(1, 999999999999), 12, '0', STR_PAD_LEFT);
    }

    /**
     * Generate fake snap token
     */
    private function generateFakeSnapToken()
    {
        return bin2hex(random_bytes(16)) . '-' . bin2hex(random_bytes(4)) . '-' . bin2hex(random_bytes(4));
    }

    /**
     * Get processed by info
     */
    private function getProcessedBy($status, $method)
    {
        if ($status === 'pending') return null;
        
        if ($method === 'manual') {
            return 'admin_' . $this->getRandomAdmin();
        }
        
        if (in_array($method, ['online_payment', 'midtrans'])) {
            return 'midtrans_webhook';
        }
        
        return 'system';
    }

    /**
     * Get random admin ID
     */
    private function getRandomAdmin()
    {
        $admin = User::where('role', 'admin')->inRandomOrder()->first();
        return $admin ? $admin->id : 1;
    }

    /**
     * Get keterangan based on status and method
     */
    private function getKeterangan($status, $method)
    {
        $keterangans = [
            'paid' => [
                'Pembayaran berhasil diproses',
                'Transaksi sukses',
                'Pembayaran lunas'
            ],
            'pending' => [
                'Menunggu konfirmasi bank',
                'Pembayaran sedang diproses',
                'Menunggu verifikasi'
            ],
            'failed' => [
                'Pembayaran gagal - saldo tidak mencukupi',
                'Transaksi ditolak oleh bank',
                'Gagal memproses pembayaran'
            ],
            'cancelled' => [
                'Dibatalkan oleh user',
                'Timeout pembayaran',
                'Dibatalkan karena kesalahan'
            ],
            'expired' => [
                'Pembayaran kedaluwarsa',
                'Melebihi batas waktu pembayaran',
                'Virtual Account expired'
            ]
        ];

        $messages = $keterangans[$status] ?? ['Pembayaran diproses'];
        return $messages[array_rand($messages)];
    }

    /**
     * Generate fake Midtrans response
     */
    private function generateMidtransResponse($status, $method, $tagihan)
    {
        if (!in_array($method, ['online_payment', 'midtrans'])) {
            return null;
        }

        $orderId = 'PAY-' . $tagihan->id . '-' . time() . '-' . rand(100, 999);
        
        return [
            'status_code' => $status === 'paid' ? '200' : ($status === 'pending' ? '201' : '400'),
            'status_message' => $status === 'paid' ? 'Success, transaction found' : 'Transaction pending',
            'transaction_id' => $status !== 'pending' ? 'TXN-' . time() . '-' . rand(1000, 9999) : null,
            'order_id' => $orderId,
            'merchant_id' => 'M001234',
            'gross_amount' => $tagihan->total_tagihan,
            'currency' => 'IDR',
            'payment_type' => 'bank_transfer',
            'transaction_time' => now()->toISOString(),
            'transaction_status' => $this->mapStatusToMidtrans($status),
            'fraud_status' => 'accept',
            'bank' => $this->getRandomBank(),
            'va_numbers' => [
                [
                    'bank' => $this->getRandomBank(),
                    'va_number' => $this->generateVANumber()
                ]
            ]
        ];
    }

    /**
     * Map internal status to Midtrans status
     */
    private function mapStatusToMidtrans($status)
    {
        $mapping = [
            'paid' => 'settlement',
            'pending' => 'pending',
            'failed' => 'deny',
            'cancelled' => 'cancel',
            'expired' => 'expire'
        ];

        return $mapping[$status] ?? 'pending';
    }
}