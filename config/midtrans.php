<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
    
    // SSL Configuration - untuk development
    'verify_ssl' => env('MIDTRANS_VERIFY_SSL', true),
    'ignore_ssl' => env('MIDTRANS_IGNORE_SSL', false), // deprecated, gunakan verify_ssl
    
    // URLs
    'snap_url' => env('MIDTRANS_IS_PRODUCTION', false) 
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js',
        
    'api_url' => env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://api.midtrans.com/v2'
        : 'https://api.sandbox.midtrans.com/v2',
        
    // cURL Options untuk SSL handling - FORCE DISABLE untuk development
    'curl_options' => [
        CURLOPT_TIMEOUT => 120,
        CURLOPT_CONNECTTIMEOUT => 60,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_SSL_VERIFYPEER => false, // FORCE FALSE untuk development
        CURLOPT_SSL_VERIFYHOST => 0,     // FORCE 0 untuk development
        CURLOPT_USERAGENT => 'Laravel-Midtrans/1.0',
        CURLOPT_ENCODING => '',
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4, // Force IPv4
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    ],
    
    // Notification/Callback URLs
    'notification_url' => env('APP_URL') . '/api/midtrans/notification',
    'finish_url' => env('APP_URL') . '/payment/finish',
    'unfinish_url' => env('APP_URL') . '/payment/unfinish',
    'error_url' => env('APP_URL') . '/payment/error',
];