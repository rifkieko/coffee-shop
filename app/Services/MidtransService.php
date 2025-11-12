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
        $timeout = (int) config('midtrans.request_timeout', 30);

        if (defined('CURLOPT_CONNECTTIMEOUT')) {
            MidtransConfig::$curlOptions[CURLOPT_CONNECTTIMEOUT] = $timeout;
        }

        if (! isset(MidtransConfig::$curlOptions[CURLOPT_HTTPHEADER])) {
            MidtransConfig::$curlOptions[CURLOPT_HTTPHEADER] = [];
        }

        if (defined('CURLOPT_SSL_VERIFYPEER') && config('app.env') !== 'production') {
            MidtransConfig::$curlOptions[CURLOPT_SSL_VERIFYPEER] = false;
        }

        if (defined('CURLOPT_SSL_VERIFYHOST') && config('app.env') !== 'production') {
            MidtransConfig::$curlOptions[CURLOPT_SSL_VERIFYHOST] = 0;
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     *
     * @throws \Midtrans\Error
     */
    public function createTransaction(array $payload): array
    {
        // Midtrans Snap::createTransaction returns a stdClass object.
        // Caller code in the app expects an array, so convert the
        // response to an array (recursively) before returning.
        $response = Snap::createTransaction($payload);

        // Convert stdClass (and nested objects) into associative arrays.
        // Using json encode/decode is a simple and safe way here since
        // the Midtrans response is JSON-serializable.
        return json_decode(json_encode($response), true);
    }
}
