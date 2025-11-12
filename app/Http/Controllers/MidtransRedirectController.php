<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MidtransRedirectController extends Controller
{
    public function finish(Request $request): View|RedirectResponse
    {
        return $this->handleRedirect($request, 'finish');
    }

    public function unfinish(Request $request): View|RedirectResponse
    {
        return $this->handleRedirect($request, 'unfinish');
    }

    public function error(Request $request): View|RedirectResponse
    {
        return $this->handleRedirect($request, 'error');
    }

    protected function handleRedirect(Request $request, string $type): View|RedirectResponse
    {
        $orderNumber = $request->query('order_id');

        if (! $orderNumber) {
            return redirect()->route('home')
                ->withErrors(__('Pesanan tidak ditemukan. Silakan mulai kembali.'));
        }

        $order = Order::with(['items.menuItem'])->where('order_number', $orderNumber)->first();

        if (! $order) {
            return redirect()->route('home')
                ->withErrors(__('Pesanan tidak ditemukan. Silakan mulai kembali.'));
        }

        $transactionStatus = $request->query('transaction_status');
        $statusMessage = $request->query('status_message');

        $alert = $this->buildAlert($order->payment_status, $type, $transactionStatus);

        return view('customer.checkout.result', [
            'order' => $order,
            'transactionStatus' => $transactionStatus,
            'statusMessage' => $statusMessage,
            'redirectType' => $type,
            'alert' => $alert,
        ]);
    }

    /**
     * @return array{title: string, message: string, tone: 'success'|'warning'|'danger'}
     */
    protected function buildAlert(?PaymentStatus $paymentStatus, string $type, ?string $transactionStatus): array
    {
        if ($type === 'finish') {
            if ($paymentStatus === PaymentStatus::Paid) {
                return [
                    'title' => __('Pembayaran berhasil!'),
                    'message' => __('Pesananmu sudah kami terima. Silakan cek status terbaru di riwayat pesanan.'),
                    'tone' => 'success',
                ];
            }

            if ($paymentStatus === PaymentStatus::Pending || $transactionStatus === 'pending') {
                return [
                    'title' => __('Pembayaran sedang diproses'),
                    'message' => __('Kami sedang menunggu konfirmasi dari Midtrans. Pesananmu otomatis diperbarui setelah pembayaran diterima.'),
                    'tone' => 'warning',
                ];
            }

            return [
                'title' => __('Pembayaran belum berhasil'),
                'message' => __('Status pembayaran belum berubah. Kamu dapat mencoba lagi atau hubungi kasir untuk bantuan.'),
                'tone' => 'danger',
            ];
        }

        if ($type === 'unfinish') {
            return [
                'title' => __('Pembayaran belum selesai'),
                'message' => __('Kamu menutup halaman pembayaran sebelum selesai. Silakan lanjutkan pembayaran dari riwayat pesanan.'),
                'tone' => 'warning',
            ];
        }

        return [
            'title' => __('Terjadi kesalahan'),
            'message' => __('Pembayaran gagal diproses. Silakan coba lagi atau hubungi kasir untuk bantuan lebih lanjut.'),
            'tone' => 'danger',
        ];
    }
}
