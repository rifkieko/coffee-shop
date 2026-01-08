<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\QrisService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private QrisService $qrisService)
    {
    }

    public function show(Order $order): View
    {
        $this->authorizeOrder($order);

        $order->load(['items.menuItem']);

        return view('customer.orders.show', compact('order'));
    }

    public function payment(Order $order): View
    {
        $this->authorizeOrder($order);

        $order->load(['items.menuItem']);

        $payableAmount = (int) round((float) $order->total_amount);
        $qrisString = null;

        try {
            $qrisString = $this->qrisService->generateFromEnv($payableAmount);
        } catch (\Throwable $exception) {
            report($exception);
        }

        return view('customer.orders.payment', [
            'order' => $order,
            'qrisString' => $qrisString,
            'payableAmount' => $payableAmount,
            'uniqueCode' => $order->payment_payload['unique_code'] ?? null,
        ]);
    }

    public function history(Request $request): View
    {
        $query = Order::query()->latest();

        if ($request->user()) {
            $query->where('user_id', $request->user()->id);
            $orders = $query->paginate(10)->withQueryString();

            return view('customer.orders.history', [
                'orders' => $orders,
                'hasQuery' => true,
            ]);
        }

        $hasQuery = false;
        $orderNumber = trim((string) $request->query('order_number', ''));
        $phoneRaw = trim((string) $request->query('phone', ''));
        $phone = preg_replace('/\D+/', '', $phoneRaw);

        if ($orderNumber !== '') {
            $hasQuery = true;
            $query->where('order_number', $orderNumber);
        } elseif ($phone !== '') {
            $hasQuery = true;
            $query->where('customer_phone', $phone);
        }

        // Fallback to session-stored recent orders for guests
        if (! $hasQuery) {
            $recentIds = (array) $request->session()->get('recent_orders', []);
            $lookupPhone = (string) $request->session()->get('order_lookup_phone', '');

            if (!empty($recentIds)) {
                $hasQuery = true;
                $query->whereIn('id', $recentIds);
            } elseif ($lookupPhone !== '') {
                $hasQuery = true;
                $query->where('customer_phone', $lookupPhone);
            }
        }

        $orders = $hasQuery ? $query->paginate(10)->withQueryString() : collect();

        return view('customer.orders.history', [
            'orders' => $orders,
            'hasQuery' => $hasQuery,
        ]);
    }

    private function authorizeOrder(Order $order): void
    {
        abort_if($order->user_id !== auth()->id(), 404);
    }
}
