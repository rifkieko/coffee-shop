<?php

return [
    'secret_key' => env('XENDIT_SECRET_KEY'),
    'public_key' => env('XENDIT_PUBLIC_KEY'),
    'callback_token' => env('XENDIT_CALLBACK_TOKEN'),
    'invoice_duration' => (int) env('XENDIT_INVOICE_DURATION', 86400),
    'payment_methods' => array_filter(array_map('trim', explode(',', (string) env('XENDIT_PAYMENT_METHODS', 'QRIS')))) ?: ['QRIS'],
    'success_url' => env('XENDIT_SUCCESS_URL'),
    'failure_url' => env('XENDIT_FAILURE_URL'),
    'request_timeout' => (int) env('XENDIT_REQUEST_TIMEOUT', 30),
    'verify_ssl' => env('XENDIT_VERIFY_SSL', false),
];
