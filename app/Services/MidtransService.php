<?php

namespace App\Services;

use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized = true;
        MidtransConfig::$is3ds = true;
        $timeout = config('midtrans.request_timeout', 30);

        MidtransConfig::$curlOptions = defined('CURLOPT_CONNECTTIMEOUT')
            ? [CURLOPT_CONNECTTIMEOUT => $timeout]
            : [];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     *
     * @throws \Midtrans\Error
     */
    public function createTransaction(array $payload): array
    {
        return Snap::createTransaction($payload);
    }
}
