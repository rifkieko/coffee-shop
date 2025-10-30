<?php

return [
    'is_production' => env('MIDTRANS_ENV', 'sandbox') === 'production',
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
    'snap_url' => env('MIDTRANS_SNAP_URL'),
    'request_timeout' => (int) env('MIDTRANS_REQUEST_TIMEOUT', 30),
    'callbacks' => [
        'notification' => env('MIDTRANS_NOTIFICATION_URL'),
        'finish' => env('MIDTRANS_FINISH_URL'),
        'unfinish' => env('MIDTRANS_UNFINISH_URL'),
        'error' => env('MIDTRANS_ERROR_URL'),
    ],
];

