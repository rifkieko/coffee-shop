<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\ShopTable;
use App\Services\OrderPlacementService;
use App\Exceptions\PaymentException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private OrderPlacementService $orderPlacementService)
    {
    }

    public function create(ShopTable $table): View
    {
        abort_if(! $table->is_active, 404);

        $categories = Category::with([
            'menuItems' => fn ($query) => $query->active()->inStock()->orderBy('name'),
        ])
            ->active()
            ->orderBy('name')
            ->get();

        return view('customer.orders.create', [
            'table' => $table,
            'categories' => $categories,
        ]);
    }

    public function store(
        Request $request,
        ShopTable $table
    ): RedirectResponse {
        abort_if(! $table->is_active, 404);

        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $order = null;

        try {
            $order = $this->orderPlacementService->place(
                $request,
                $validated['items'] ?? [],
                $table,
                $validated['notes'] ?? null,
                $request->user(),
                [
                    'customer_name' => $request->user()?->name,
                    'customer_email' => $request->user()?->email,
                    'customer_phone' => $request->user()?->phone,
                ]
            );
        } catch (ValidationException $exception) {
            return redirect()->back()
                ->withErrors($exception->errors())
                ->withInput();
        } catch (PaymentException $exception) {
            report($exception);

            $order = $exception->order() ?? $order;

            if ($order) {
                return redirect()->route('customer.orders.show', $order)
                    ->withErrors('Tidak dapat membuat transaksi pembayaran. Silakan hubungi kasir untuk melanjutkan.');
            }

            return redirect()->back()
                ->withErrors('Tidak dapat membuat transaksi pembayaran. Silakan hubungi kasir untuk melanjutkan.');
        }

        return redirect()->route('customer.orders.payment', $order)
            ->with('status', 'Pesanan berhasil dibuat. Silakan selesaikan pembayaran.');
    }

    public function show(Order $order): View
    {
        $this->authorizeOrder($order);

        $order->load(['items.menuItem', 'table']);

        return view('customer.orders.show', compact('order'));
    }

    public function payment(Order $order): View
    {
        $this->authorizeOrder($order);

        abort_if(! $order->midtrans_token, 404);

        return view('customer.orders.payment', [
            'order' => $order->load(['items.menuItem', 'table']),
            'midtransClientKey' => config('midtrans.client_key'),
        ]);
    }

    public function history(Request $request): View
    {
        $orders = Order::with(['table'])
            ->where('user_id', $request->user()?->id)
            ->latest()
            ->paginate(10);

        return view('customer.orders.history', compact('orders'));
    }

    private function authorizeOrder(Order $order): void
    {
        abort_if($order->user_id !== auth()->id(), 404);
    }
}
