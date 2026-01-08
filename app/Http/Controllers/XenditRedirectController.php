<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class XenditRedirectController extends Controller
{
    public function success(Request $request, ?string $orderNumber = null): View|RedirectResponse
    {
        return $this->handleRedirect($request, $orderNumber, 'success');
    }

    public function failed(Request $request, ?string $orderNumber = null): View|RedirectResponse
    {
        return $this->handleRedirect($request, $orderNumber, 'failed');
    }

    protected function handleRedirect(Request $request, ?string $orderNumber, string $type): View|RedirectResponse
    {
        $orderNumber = $orderNumber ?: $request->query('order_id');

        if (! $orderNumber) {
            return redirect()->route('home')
                ->withErrors(__('Pesanan tidak ditemukan. Silakan mulai kembali.'));
        }

        $order = Order::with(['items.menuItem'])->where('order_number', $orderNumber)->first();

        if (! $order) {
            return redirect()->route('home')
                ->withErrors(__('Pesanan tidak ditemukan. Silakan mulai kembali.'));
        }

        $invoiceStatus = strtoupper((string) $request->query('status', ''));
        $statusMessage = $request->query('message');

        $alert = $this->buildAlert($order->payment_status, $type, $invoiceStatus);

        return view('customer.checkout.result', [
            'order' => $order,
            'transactionStatus' => $invoiceStatus,
            'statusMessage' => $statusMessage,
            'redirectType' => $type,
            'alert' => $alert,
        ]);
    }

    /**
     * @return array{title: string, message: string, tone: 'success'|'warning'|'danger'}
     */
    protected function buildAlert(?PaymentStatus $paymentStatus, string $type, ?string $invoiceStatus): array
    {
        if ($type === 'success') {
            if ($paymentStatus === PaymentStatus::Paid || $invoiceStatus === 'PAID') {
                return [
                    'title' => __('Pembayaran berhasil!'),
                    'message' => __('Pesananmu sudah kami terima. Silakan cek status terbaru di riwayat pesanan.'),
                    'tone' => 'success',
                ];
            }

            if ($paymentStatus === PaymentStatus::Pending || $invoiceStatus === 'PENDING') {
                return [
                    'title' => __('Pembayaran sedang diproses'),
                    'message' => __('Kami sedang menunggu konfirmasi dari Xendit. Pesananmu otomatis diperbarui setelah pembayaran diterima.'),
                    'tone' => 'warning',
                ];
            }

            return [
                'title' => __('Pembayaran belum berhasil'),
                'message' => __('Status pembayaran belum berubah. Kamu dapat mencoba lagi atau hubungi kasir untuk bantuan.'),
                'tone' => 'danger',
            ];
        }

        return [
            'title' => __('Pembayaran gagal atau dibatalkan'),
            'message' => __('Pembayaran gagal diproses. Silakan coba lagi atau hubungi kasir untuk bantuan lebih lanjut.'),
            'tone' => 'danger',
        ];
    }
}
