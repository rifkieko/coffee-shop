<?php

namespace App\Services;

use App\Models\Order;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Xendit\Configuration;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\InvoiceApi;
use Xendit\XenditSdkException;

class XenditService
{
    private InvoiceApi $invoiceApi;

    public function __construct()
    {
        Configuration::setXenditKey((string) config('xendit.secret_key'));

        $guzzleOptions = [
            'timeout' => (int) config('xendit.request_timeout', 30),
        ];

        // Disable SSL verification on non-production to avoid local CA issues.
        if (config('app.env') !== 'production') {
            $guzzleOptions['verify'] = (bool) config('xendit.verify_ssl', false);
        }

        $this->invoiceApi = new InvoiceApi(new Client($guzzleOptions));
    }

    /**
     * @param  Collection<int, \App\Models\MenuItem>  $menuItems
     * @param  Collection<int|string, int>  $quantities
     * @param  array<string, mixed>  $customerData
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     *
     * @throws XenditSdkException
     */
    public function createInvoice(
        Order $order,
        Collection $menuItems,
        Collection $quantities,
        array $customerData,
        array $options = [],
    ): array {
        $customerName = $customerData['customer_name'] ?? $order->customer_name;
        $customerEmail = $customerData['customer_email'] ?? $order->customer_email;
        $customerPhone = $customerData['customer_phone'] ?? $order->customer_phone;

        $successUrl = $options['success_url'] ?? config('xendit.success_url') ?? route('xendit.success', ['orderNumber' => $order->order_number]);
        $failureUrl = $options['failure_url'] ?? config('xendit.failure_url') ?? route('xendit.failed', ['orderNumber' => $order->order_number]);

        $items = $menuItems->map(function ($menuItem) use ($quantities) {
            return [
                'name' => $menuItem->name,
                'price' => (float) $menuItem->price,
                'quantity' => (float) ($quantities[$menuItem->id] ?? 1),
                'reference_id' => (string) $menuItem->id,
            ];
        })->values()->all();

        $payload = [
            'external_id' => $order->order_number,
            'amount' => (float) $order->total_amount,
            'description' => 'Pembayaran pesanan '.$order->order_number,
            'payer_email' => $customerEmail,
            'invoice_duration' => (int) config('xendit.invoice_duration', 86400),
            'should_send_email' => false,
            'success_redirect_url' => $successUrl,
            'failure_redirect_url' => $failureUrl,
            'payment_methods' => config('xendit.payment_methods') ?: ['QRIS'],
            'customer' => [
                'given_names' => $customerName,
                'email' => $customerEmail,
                'mobile_number' => $customerPhone,
            ],
            'items' => $items,
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ],
        ];

        if (empty($customerEmail)) {
            unset($payload['payer_email']);
            unset($payload['customer']['email']);
        }

        if (empty($customerPhone)) {
            unset($payload['customer']['mobile_number']);
        }

        $invoice = $this->invoiceApi->createInvoice(new CreateInvoiceRequest($payload));

        return json_decode(json_encode($invoice), true);
    }
}
