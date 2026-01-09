<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        $this->initializeConfig();
    }

    private function initializeConfig()
    {
        try {
            // Validasi konfigurasi dasar
            $serverKey = config('midtrans.server_key');
            $clientKey = config('midtrans.client_key');
            
            if (empty($serverKey)) {
                throw new \Exception('Server key Midtrans tidak ditemukan. Periksa file .env');
            }
            
            if (empty($clientKey)) {
                throw new \Exception('Client key Midtrans tidak ditemukan. Periksa file .env');
            }

            // Set basic configuration
            Config::$serverKey = $serverKey;
            Config::$isProduction = config('midtrans.is_production', false);
            Config::$isSanitized = config('midtrans.is_sanitized', true);
            Config::$is3ds = config('midtrans.is_3ds', true);

            // Set cURL options untuk handle SSL dan timeout
            Config::$curlOptions = [
                CURLOPT_TIMEOUT => 60,
                CURLOPT_CONNECTTIMEOUT => 30,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_SSL_VERIFYPEER => config('midtrans.verify_ssl', false), // False untuk development
                CURLOPT_SSL_VERIFYHOST => config('midtrans.verify_ssl', false) ? 2 : 0,
                CURLOPT_USERAGENT => 'Laravel-Midtrans/1.0',
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'Authorization: Basic ' . base64_encode($serverKey . ':')
                ]
            ];

            Log::info('Midtrans Config Initialized Successfully', [
                'is_production' => Config::$isProduction,
                'server_key_length' => strlen($serverKey),
                'ssl_verify' => config('midtrans.verify_ssl', false)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to initialize Midtrans config', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create Snap Token for payment
     */
    public function createSnapToken($transactionDetails, $customerDetails, $itemDetails)
    {
        try {
            // Validasi input
            $this->validateTransactionData($transactionDetails, $customerDetails, $itemDetails);

            $params = [
                'transaction_details' => $transactionDetails,
                'customer_details' => $customerDetails,
                'item_details' => $itemDetails,
                'callbacks' => [
                    'finish' => route('pelanggan.bayar.callback'),
                    'unfinish' => route('pelanggan.tagihan.index'),
                    'error' => route('pelanggan.tagihan.index')
                ]
            ];

            Log::info('Creating Snap Token', [
                'order_id' => $transactionDetails['order_id'],
                'amount' => $transactionDetails['gross_amount'],
                'customer_email' => $customerDetails['email'] ?? 'unknown'
            ]);

            // Debug: Log full params untuk debugging
            Log::debug('Midtrans Snap Params', [
                'params' => $params,
                'config' => [
                    'server_key' => substr(Config::$serverKey, 0, 10) . '...',
                    'is_production' => Config::$isProduction
                ]
            ]);

            $snapToken = Snap::getSnapToken($params);

            if (empty($snapToken)) {
                throw new \Exception('Snap token kosong dari response Midtrans');
            }

            Log::info('Snap Token Created Successfully', [
                'order_id' => $transactionDetails['order_id'],
                'token_length' => strlen($snapToken)
            ]);

            return $snapToken;

        } catch (\Midtrans\Exception\Curl $e) {
            Log::error('Midtrans cURL Error', [
                'error' => $e->getMessage(),
                'order_id' => $transactionDetails['order_id'] ?? 'unknown',
                'curl_info' => $this->extractCurlInfo($e)
            ]);
            
            throw new \Exception('Koneksi ke payment gateway gagal: ' . $this->getFriendlyErrorMessage($e));
            
        } catch (\Midtrans\Exception\Api $e) {
            Log::error('Midtrans API Error', [
                'error' => $e->getMessage(),
                'order_id' => $transactionDetails['order_id'] ?? 'unknown',
                'response_body' => method_exists($e, 'getResponseBody') ? $e->getResponseBody() : 'No response body'
            ]);
            
            throw new \Exception('Error dari payment gateway: ' . $this->getFriendlyErrorMessage($e));
            
        } catch (\Exception $e) {
            Log::error('Generic Snap Token Error', [
                'error' => $e->getMessage(),
                'order_id' => $transactionDetails['order_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception('Gagal membuat token pembayaran: ' . $this->getFriendlyErrorMessage($e));
        }
    }

    /**
     * Validate transaction data
     */
    private function validateTransactionData($transactionDetails, $customerDetails, $itemDetails)
    {
        // Validasi transaction_details
        if (empty($transactionDetails['order_id'])) {
            throw new \Exception('Order ID tidak boleh kosong');
        }
        
        if (empty($transactionDetails['gross_amount']) || $transactionDetails['gross_amount'] <= 0) {
            throw new \Exception('Jumlah pembayaran tidak valid');
        }

        // Validasi customer_details
        if (empty($customerDetails['first_name'])) {
            throw new \Exception('Nama customer tidak boleh kosong');
        }
        
        if (empty($customerDetails['email']) || !filter_var($customerDetails['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Email customer tidak valid');
        }

        // Validasi item_details
        if (empty($itemDetails) || !is_array($itemDetails)) {
            throw new \Exception('Detail item tidak valid');
        }

        foreach ($itemDetails as $item) {
            if (empty($item['id']) || empty($item['name']) || empty($item['price']) || empty($item['quantity'])) {
                throw new \Exception('Detail item tidak lengkap');
            }
        }
    }

    /**
     * Get friendly error message
     */
    private function getFriendlyErrorMessage($exception)
    {
        $message = $exception->getMessage();
        
        // Handle common error codes/messages
        if (strpos($message, 'SSL') !== false) {
            return 'Masalah koneksi SSL. Silakan coba lagi atau hubungi administrator.';
        }
        
        if (strpos($message, 'timeout') !== false) {
            return 'Koneksi timeout. Silakan periksa koneksi internet dan coba lagi.';
        }
        
        if (strpos($message, 'Invalid credentials') !== false) {
            return 'Konfigurasi payment gateway tidak valid. Hubungi administrator.';
        }
        
        if (strpos($message, 'duplicate') !== false || strpos($message, 'already exists') !== false) {
            return 'Order ID sudah ada. Silakan refresh halaman dan coba lagi.';
        }

        // Handle array key errors (seperti error 10023)
        if (strpos($message, 'Undefined array key') !== false || strpos($message, 'Undefined index') !== false) {
            return 'Response dari payment gateway tidak lengkap. Silakan coba lagi.';
        }
        
        return 'Terjadi kesalahan: ' . $message;
    }

    /**
     * Extract cURL information from exception
     */
    private function extractCurlInfo($exception)
    {
        $message = $exception->getMessage();
        
        return [
            'ssl_error' => strpos($message, 'SSL') !== false,
            'timeout_error' => strpos($message, 'timeout') !== false,
            'connection_error' => strpos($message, 'connect') !== false,
            'message' => $message
        ];
    }

    /**
     * Get transaction status from Midtrans
     */
    public function getTransactionStatus($orderId)
    {
        try {
            Log::info('Getting transaction status', ['order_id' => $orderId]);

            $status = Transaction::status($orderId);

            Log::info('Transaction status retrieved', [
                'order_id' => $orderId,
                'status' => $status->transaction_status ?? 'unknown',
                'payment_type' => $status->payment_type ?? 'unknown'
            ]);

            return $status;

        } catch (\Exception $e) {
            Log::error('Failed to get transaction status', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            throw new \Exception('Gagal mendapatkan status transaksi: ' . $this->getFriendlyErrorMessage($e));
        }
    }

    /**
     * Test connection to Midtrans
     */
    public function testConnection()
    {
        try {
            // Simple test dengan parameter minimal
            $testParams = [
                'transaction_details' => [
                    'order_id' => 'test-' . time() . '-' . rand(1000, 9999),
                    'gross_amount' => 10000,
                ],
                'customer_details' => [
                    'first_name' => 'Test',
                    'last_name' => 'User',
                    'email' => 'test@example.com',
                ],
                'item_details' => [
                    [
                        'id' => 'test-item',
                        'price' => 10000,
                        'quantity' => 1,
                        'name' => 'Test Item',
                    ],
                ],
            ];

            $snapToken = Snap::getSnapToken($testParams);
            
            return [
                'success' => true,
                'message' => 'Connection successful',
                'token_created' => !empty($snapToken),
                'token_length' => strlen($snapToken ?? '')
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'friendly_message' => $this->getFriendlyErrorMessage($e),
                'curl_info' => method_exists($e, 'getMessage') ? $this->extractCurlInfo($e) : null
            ];
        }
    }
}