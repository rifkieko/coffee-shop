<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class XenditWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $callbackToken = $request->header('x-callback-token') ?? $request->header('X-Callback-Token');

        if (! $callbackToken || $callbackToken !== config('xendit.callback_token')) {
            return response('Invalid callback token', 403);
        }

        $payload = $request->all();
        $data = is_array($payload['data'] ?? null) ? $payload['data'] : $payload;

        $order = Order::where('order_number', $data['external_id'] ?? null)->first();

        if (! $order) {
            return response('Order not found', 404);
        }

        $status = strtoupper((string) ($data['status'] ?? ''));
        $paidAmount = (float) ($data['paid_amount'] ?? $data['amount'] ?? 0);
        $invoiceId = $data['id'] ?? null;
        $invoiceUrl = $data['invoice_url'] ?? null;
        $expiresAt = $data['expiry_date'] ?? null;

        $baseUpdate = [
            'payment_payload' => $payload,
        ];

        if ($invoiceId) {
            $baseUpdate['xendit_invoice_id'] = $invoiceId;
        }

        if ($invoiceUrl) {
            $baseUpdate['xendit_invoice_url'] = $invoiceUrl;
        }

        if ($expiresAt) {
            $baseUpdate['expires_at'] = Carbon::parse($expiresAt);
        }

        switch ($status) {
            case 'PAID':
            case 'SETTLED':
                $order->markAsPaid(
                    $paidAmount > 0 ? $paidAmount : (float) $order->total_amount,
                    $payload,
                    $baseUpdate
                );
                break;
            case 'PENDING':
                $order->update($baseUpdate + [
                    'payment_status' => PaymentStatus::Pending,
                ]);
                break;
            case 'EXPIRED':
                $order->update($baseUpdate + [
                    'payment_status' => PaymentStatus::Expired,
                    'status' => OrderStatus::Cancelled,
                ]);
                break;
            default:
                $order->update($baseUpdate + [
                    'payment_status' => PaymentStatus::Failed,
                    'status' => OrderStatus::Cancelled,
                ]);
                break;
        }

        return response('OK', 200);
    }
}
