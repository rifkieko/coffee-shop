<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $paymentStatus = $request->query('payment_status');

        $orders = Order::with(['user'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($paymentStatus, fn ($query) => $query->where('payment_status', $paymentStatus))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'status' => $status,
            'paymentStatus' => $paymentStatus,
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['items.menuItem', 'user']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_column(OrderStatus::cases(), 'value'))],
        ]);

        $status = OrderStatus::from($validated['status']);

        $order->update([
            'status' => $status,
        ]);

        return redirect()->route('admin.orders.show', $order)
            ->with('status', 'Status pesanan berhasil diperbarui.');
    }

    public function updatePaymentStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'payment_status' => ['required', 'in:'.implode(',', array_column(PaymentStatus::cases(), 'value'))],
        ]);

        $paymentStatus = PaymentStatus::from($validated['payment_status']);

        if ($paymentStatus === PaymentStatus::Paid) {
            $order->markAsPaid($order->total_amount, [
                'source' => 'manual',
                'updated_by' => $request->user()?->id,
            ]);
        } else {
            $order->update([
                'payment_status' => $paymentStatus,
            ]);
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('status', 'Status pembayaran berhasil diperbarui.');
    }
}
