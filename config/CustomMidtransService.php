<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class CustomMidtransService
{
    private $serverKey;
    private $clientKey;
    private $isProduction;
    private $apiUrl;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key');
        $this->clientKey = config('midtrans.client_key');
        $this->isProduction = config('midtrans.is_production', false);
        $this->apiUrl = $this->isProduction 
            ? 'https://api.midtrans.com/v2' 
            : 'https://api.sandbox.midtrans.com/v2';

        if (empty($this->serverKey)) {
            throw new \Exception('Midtrans server key tidak ditemukan');
        }
    }

    /**
     * Create Snap Token menggunakan cURL manual
     */
    public function createSnapToken($transactionDetails, $customerDetails, $itemDetails)
    {
        try {
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

            Log::info('Creating Snap Token with manual cURL', [
                'order_id' => $transactionDetails['order_id'],
                'amount' => $transactionDetails['gross_amount']
            ]);

            $snapUrl = $this->isProduction 
                ? 'https://app.midtrans.com/snap/v1/transactions'
                : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

            $response = $this->makeRequest('POST', $snapUrl, $params);

            if (!isset($response['token'])) {
                Log::error('Invalid response from Midtrans', [
                    'response' => $response,
                    'order_id' => $transactionDetails['order_id']
                ]);
                throw new \Exception('Response tidak valid dari Midtrans: ' . json_encode($response));
            }

            Log::info('Snap Token created successfully with manual cURL', [
                'order_id' => $transactionDetails['order_id'],
                'token_length' => strlen($response['token'])
            ]);

            return $response['token'];

        } catch (\Exception $e) {
            Log::error('Failed to create snap token with manual cURL', [
                'error' => $e->getMessage(),
                'order_id' => $transactionDetails['order_id'] ?? 'unknown'
            ]);
            throw $e;
        }
    }

    /**
     * Get transaction status
     */
    public function getTransactionStatus($orderId)
    {
        try {
            $url = $this->apiUrl . '/' . $orderId . '/status';
            $response = $this->makeRequest('GET', $url);

            Log::info('Transaction status retrieved with manual cURL', [
                'order_id' => $orderId,
                'status' => $response['transaction_status'] ?? 'unknown'
            ]);

            return (object) $response;

        } catch (\Exception $e) {
            Log::error('Failed to get transaction status', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Make HTTP request dengan cURL manual (SSL disabled)
     */
    private function makeRequest($method, $url, $data = null)
    {
        $ch = curl_init();

        // Basic cURL options
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_CONNECTTIMEOUT => 60,
            
            // SSL Options - FORCE DISABLE
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            
            // Connection options
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_ENCODING => '',
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            
            // Headers
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($this->serverKey . ':'),
                'User-Agent: Laravel-Custom-Midtrans/1.0'
            ]
        ]);

        // Set method and data
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method === 'GET') {
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        }

        // Execute request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // Handle cURL errors
        if ($error) {
            Log::error('cURL Error in custom service', [
                'error' => $error,
                'url' => $url,
                'method' => $method
            ]);
            throw new \Exception('Connection error: ' . $error);
        }

        // Handle HTTP errors
        if ($httpCode < 200 || $httpCode >= 300) {
            Log::error('HTTP Error in custom service', [
                'http_code' => $httpCode,
                'response' => $response,
                'url' => $url,
                'method' => $method
            ]);
            throw new \Exception('HTTP Error ' . $httpCode . ': ' . $response);
        }

        // Parse JSON response
        $decodedResponse = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON decode error', [
                'response' => $response,
                'json_error' => json_last_error_msg()
            ]);
            throw new \Exception('Invalid JSON response: ' . json_last_error_msg());
        }

        return $decodedResponse;
    }

    /**
     * Test connection
     */
    public function testConnection()
    {
        try {
            $testParams = [
                'transaction_details' => [
                    'order_id' => 'test-custom-' . time(),
                    'gross_amount' => 10000,
                ],
                'customer_details' => [
                    'first_name' => 'Test',
                    'email' => 'test@example.com',
                ],
                'item_details' => [
                    [
                        'id' => 'test',
                        'price' => 10000,
                        'quantity' => 1,
                        'name' => 'Test Item',
                    ],
                ],
            ];

            $token = $this->createSnapToken(
                $testParams['transaction_details'],
                $testParams['customer_details'],
                $testParams['item_details']
            );

            return [
                'success' => true,
                'message' => 'Custom cURL connection successful',
                'token_created' => !empty($token)
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}