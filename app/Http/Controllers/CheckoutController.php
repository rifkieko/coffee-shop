<?php

namespace App\Http\Controllers;

use App\Exceptions\PaymentException;
use App\Models\Cart;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderPlacementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private OrderPlacementService $orderPlacementService
    ) {
    }

    public function show(Request $request): RedirectResponse|View
    {
        $cart = $this->cartService->getActiveCart($request);

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->withErrors(__('Keranjang masih kosong. Tambahkan menu terlebih dahulu.'));
        }

        return view('customer.checkout.index', [
            'cart' => $cart,
            'user' => $request->user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $cart = $this->cartService->getActiveCart($request);

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->withErrors(__('Keranjang masih kosong. Tambahkan menu terlebih dahulu.'));
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:191'],
            'phone' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $items = $cart->items->mapWithKeys(fn ($item) => [
            $item->menu_item_id => $item->quantity,
        ])->toArray();

        $order = null;

        try {
            $order = $this->orderPlacementService->place(
                $request,
                $items,
                table: null,
                notes: $validated['notes'] ?? null,
                user: $request->user(),
                customerData: [
                    'customer_name' => $validated['name'],
                    'customer_email' => $validated['email'],
                    'customer_phone' => $validated['phone'],
                ]
            );
        } catch (ValidationException $exception) {
            return redirect()->route('cart.index')
                ->withErrors($exception->errors())
                ->withInput();
        } catch (PaymentException $exception) {
            report($exception);
            $order = $exception->order();

            if ($order) {
                $this->finalizeCart($request, $cart);

                if ($order->midtrans_token) {
                    return redirect()->route('checkout.payment', [
                        'order' => $order,
                        'token' => $order->midtrans_token,
                    ])->withErrors(__('Tidak dapat membuat transaksi pembayaran secara otomatis. Silakan coba lagi dari halaman pembayaran.'));
                }

                return redirect()->route('home')
                    ->withErrors(__('Pesanan berhasil dibuat, namun pembayaran belum dapat diproses. Silakan hubungi kasir.'));
            }

            return redirect()->route('cart.index')
                ->withErrors(__('Tidak dapat memproses pembayaran. Silakan coba kembali.'));
        }

        $this->finalizeCart($request, $cart);

        if (! $order->midtrans_token) {
            return redirect()->route('home')
                ->withErrors(__('Pesanan berhasil dibuat, namun pembayaran belum dapat diproses. Silakan hubungi kasir.'));
        }

        return redirect()->route('checkout.payment', [
            'order' => $order,
            'token' => $order->midtrans_token,
        ])->with('status', __('Pesanan berhasil dibuat. Lanjutkan ke pembayaran.'));
    }

    public function payment(Order $order, string $token): View
    {
        abort_if($order->midtrans_token !== $token, 404);

        $order->load('items.menuItem');

        return view('customer.checkout.payment', [
            'order' => $order,
            'midtransClientKey' => config('midtrans.client_key'),
        ]);
    }

    protected function finalizeCart(Request $request, Cart $cart): void
    {
        $cart->markAsSubmitted();
        $request->session()->forget('cart_token');
    }
}
