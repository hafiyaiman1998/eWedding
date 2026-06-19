<?php

return [
    /*
    |--------------------------------------------------------------------------
    | toyyibPay Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for toyyibPay payment gateway integration
    |
    */

    'sandbox' => env('TOYYIBPAY_SANDBOX', true),
    
    'user_secret_key' => env('TOYYIBPAY_USER_SECRET_KEY'),
    
    'category_code' => env('TOYYIBPAY_CATEGORY_CODE'),
    
    'return_url' => env('TOYYIBPAY_RETURN_URL', env('APP_URL') . '/gift/return'),
    
    'callback_url' => env('TOYYIBPAY_CALLBACK_URL', env('APP_URL') . '/gift/callback'),
    
    /*
    |--------------------------------------------------------------------------
    | API URLs
    |--------------------------------------------------------------------------
    */
    'api_urls' => [
        'sandbox' => 'https://dev.toyyibpay.com/index.php/api/',
        'production' => 'https://toyyibpay.com/index.php/api/',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Payment URLs
    |--------------------------------------------------------------------------
    */
    'payment_urls' => [
        'sandbox' => 'https://dev.toyyibpay.com/',
        'production' => 'https://toyyibpay.com/',
    ],
]; 