<?php

namespace App\Http\Controllers\Debug;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransDebugController extends Controller
{
    public function testConfig()
    {
        $serverKey = config('midtrans.server_key');
        $clientKey = config('midtrans.client_key');
        
        return response()->json([
            'success' => true,
            'config' => [
                'server_key_exists' => !empty($serverKey),
                'server_key_length' => strlen($serverKey ?? ''),
                'server_key_prefix' => $serverKey ? substr($serverKey, 0, 15) . '...' : 'NULL',
                'client_key_exists' => !empty($clientKey),
                'client_key_length' => strlen($clientKey ?? ''),
                'is_production' => config('midtrans.is_production'),
                'ignore_ssl' => config('midtrans.ignore_ssl'),
            ],
            'env_check' => [
                'MIDTRANS_SERVER_KEY' => env('MIDTRANS_SERVER_KEY') ? 'SET' : 'NOT SET',
                'MIDTRANS_CLIENT_KEY' => env('MIDTRANS_CLIENT_KEY') ? 'SET' : 'NOT SET',
                'MIDTRANS_IS_PRODUCTION' => env('MIDTRANS_IS_PRODUCTION', 'false'),
                'MIDTRANS_IGNORE_SSL' => env('MIDTRANS_IGNORE_SSL', 'true'),
            ]
        ], 200, [], JSON_PRETTY_PRINT);
    }
    
    public function testMidtransAPI()
    {
        try {
            $serverKey = config('midtrans.server_key');
            
            if (empty($serverKey)) {
                throw new \Exception('Server key tidak ditemukan');
            }
            
            $testPayload = [
                'transaction_details' => [
                    'order_id' => 'DEBUG-TEST-' . time(),
                    'gross_amount' => 10000,
                ]
            ];
            
            Log::info('Testing Midtrans API with SSL bypass', $testPayload);
            
            // HTTP Client dengan SSL verification disabled untuk development
            $response = Http::timeout(30)
                ->withOptions([
                    'verify' => false, // Disable SSL verification untuk development
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                    ]
                ])
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode($serverKey . ':'),
                    'User-Agent' => 'BPABS-Debug/1.0'
                ])
                ->post('https://app.sandbox.midtrans.com/snap/v1/transactions', $testPayload);
            
            $responseData = $response->json();
            
            return response()->json([
                'success' => $response->successful(),
                'http_status' => $response->status(),
                'request_payload' => $testPayload,
                'response_body' => $responseData,
                'server_key_format' => substr($serverKey, 0, 15) . '...',
                'ssl_disabled' => true,
                'has_token' => isset($responseData['token']),
                'token_preview' => isset($responseData['token']) ? substr($responseData['token'], 0, 20) . '...' : null,
            ], 200, [], JSON_PRETTY_PRINT);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'ssl_note' => 'SSL verification disabled for development'
            ], 500, [], JSON_PRETTY_PRINT);
        }
    }
    
    public function testWithCustomerDetails()
    {
        try {
            $serverKey = config('midtrans.server_key');
            
            if (empty($serverKey)) {
                throw new \Exception('Server key tidak ditemukan');
            }
            
            $testPayload = [
                'transaction_details' => [
                    'order_id' => 'DEBUG-CUSTOMER-' . time(),
                    'gross_amount' => 50000,
                ],
                'customer_details' => [
                    'first_name' => 'Debug',
                    'last_name' => 'Test',
                    'email' => 'debug@bpabs.com',
                    'phone' => '081234567890',
                ]
            ];
            
            // HTTP Client dengan SSL bypass
            $response = Http::timeout(30)
                ->withOptions([
                    'verify' => false,
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                    ]
                ])
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode($serverKey . ':'),
                ])
                ->post('https://app.sandbox.midtrans.com/snap/v1/transactions', $testPayload);
            
            $responseData = $response->json();
            
            return response()->json([
                'success' => $response->successful(),
                'http_status' => $response->status(),
                'request_payload' => $testPayload,
                'response_body' => $responseData,
                'has_token' => isset($responseData['token']),
                'token_length' => isset($responseData['token']) ? strlen($responseData['token']) : 0,
                'ssl_disabled' => true,
            ], 200, [], JSON_PRETTY_PRINT);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500, [], JSON_PRETTY_PRINT);
        }
    }
    
    public function testSDKOriginal()
    {
        try {
            $serverKey = config('midtrans.server_key');
            
            if (empty($serverKey)) {
                throw new \Exception('Server key tidak ditemukan');
            }
            
            // Test original Midtrans SDK dengan SSL bypass
            \Midtrans\Config::$serverKey = $serverKey;
            \Midtrans\Config::$isProduction = false;
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;
            
            // Disable SSL verification untuk development
            \Midtrans\Config::$curlOptions = [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTPHEADER => [],
            ];
            
            $testPayload = [
                'transaction_details' => [
                    'order_id' => 'SDK-TEST-' . time(),
                    'gross_amount' => 75000,
                ],
                'customer_details' => [
                    'first_name' => 'SDK Test',
                    'email' => 'sdktest@bpabs.com',
                    'phone' => '081234567890',
                ]
            ];
            
            Log::info('Testing original Midtrans SDK with SSL bypass', $testPayload);
            
            $snapToken = \Midtrans\Snap::getSnapToken($testPayload);
            
            return response()->json([
                'success' => true,
                'method' => 'Original Midtrans SDK',
                'snap_token' => $snapToken,
                'token_length' => strlen($snapToken),
                'params_sent' => $testPayload,
                'ssl_disabled' => true,
                'config_used' => [
                    'server_key_prefix' => substr($serverKey, 0, 15) . '...',
                    'is_production' => \Midtrans\Config::$isProduction,
                    'is_sanitized' => \Midtrans\Config::$isSanitized,
                    'is_3ds' => \Midtrans\Config::$is3ds,
                ]
            ], 200, [], JSON_PRETTY_PRINT);
            
        } catch (\Midtrans\Exception $e) {
            return response()->json([
                'success' => false,
                'method' => 'Original Midtrans SDK',
                'error_type' => 'Midtrans Exception',
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'ssl_note' => 'SSL verification disabled but still error'
            ], 500, [], JSON_PRETTY_PRINT);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'method' => 'Original Midtrans SDK', 
                'error_type' => 'General Exception',
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500, [], JSON_PRETTY_PRINT);
        }
    }
}