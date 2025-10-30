<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MidtransWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $payload = $request->all();

        if (! $this->isSignatureValid($payload)) {
            return response('Invalid signature', 403);
        }

        $order = Order::where('order_number', $payload['order_id'] ?? null)->first();

        if (! $order) {
            return response('Order not found', 404);
        }

        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;
        $grossAmount = (float) ($payload['gross_amount'] ?? 0);

        match ($transactionStatus) {
            'capture' => $this->handleCapture($order, $fraudStatus, $grossAmount, $payload),
            'settlement' => $this->handleSettlement($order, $grossAmount, $payload),
            'pending' => $order->update([
                'payment_status' => PaymentStatus::Pending,
                'payment_payload' => $payload,
            ]),
            'deny', 'cancel' => $order->update([
                'payment_status' => PaymentStatus::Failed,
                'status' => OrderStatus::Cancelled,
                'payment_payload' => $payload,
            ]),
            'expire' => $order->update([
                'payment_status' => PaymentStatus::Expired,
                'status' => OrderStatus::Cancelled,
                'payment_payload' => $payload,
            ]),
            'refund', 'partial_refund' => $order->update([
                'payment_status' => PaymentStatus::Failed,
                'status' => OrderStatus::Cancelled,
                'payment_payload' => $payload,
            ]),
            default => null,
        };

        return response('OK', 200);
    }

    private function handleCapture(Order $order, ?string $fraudStatus, float $grossAmount, array $payload): void
    {
        if ($fraudStatus === 'challenge') {
            $order->update([
                'payment_status' => PaymentStatus::Pending,
                'payment_payload' => $payload,
            ]);
        } elseif ($fraudStatus === 'accept') {
            $order->markAsPaid($grossAmount, $payload);
        }
    }

    private function handleSettlement(Order $order, float $grossAmount, array $payload): void
    {
        $order->markAsPaid($grossAmount, $payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function isSignatureValid(array $payload): bool
    {
        if (
            empty($payload['order_id']) ||
            empty($payload['status_code']) ||
            empty($payload['gross_amount']) ||
            empty($payload['signature_key'])
        ) {
            return false;
        }

        $serverKey = config('midtrans.server_key');

        if (! $serverKey) {
            return false;
        }

        $signature = hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].$serverKey);

        return hash_equals($signature, $payload['signature_key']);
    }
}

