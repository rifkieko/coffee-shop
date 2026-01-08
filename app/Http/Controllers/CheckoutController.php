<?php

namespace App\Http\Controllers;

use App\Exceptions\PaymentException;
use App\Models\Cart;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderPlacementService;
use Illuminate\Http\JsonResponse;
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

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $cart = $this->cartService->getActiveCart($request);

        if ($cart->items->isEmpty()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('Keranjang masih kosong. Tambahkan menu terlebih dahulu.'),
                ], 422);
            }

            return redirect()->route('cart.index')
                ->withErrors(__('Keranjang masih kosong. Tambahkan menu terlebih dahulu.'));
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:191'],
            // Phone as digits-only string with length limit
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9]+$/'],
            'table_number' => ['required', 'integer', 'min:1', 'max:9999'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'phone.regex' => "Nomer Telfon harus diisi angka",
        ]);

        $order = null;

        try {
            $order = $this->orderPlacementService->placeFromCart(
                $request,
                $cart,
                notes: $validated['notes'] ?? null,
                user: $request->user(),
                customerData: [
                    'customer_name' => $validated['name'],
                    'customer_email' => $validated['email'],
                    'customer_phone' => $validated['phone'],
                ],
                tableNumber: (int) ($validated['table_number'] ?? 0),
            );
        } catch (ValidationException $exception) {
            if ($request->expectsJson()) {
                throw $exception;
            }

            return redirect()->route('cart.index')
                ->withErrors($exception->errors())
                ->withInput();
        } catch (PaymentException $exception) {
            report($exception);
            $order = $exception->order();

            if ($order) {
                $this->finalizeCart($request, $cart);
                $this->rememberOrderForGuest($request, $order, $validated['phone'] ?? null);

                if ($order->xendit_invoice_url) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => __('Tidak dapat membuat transaksi pembayaran secara otomatis. Silakan coba lagi dari halaman pembayaran.'),
                            'checkout_payment_url' => route('checkout.payment', [
                                'order' => $order,
                                'invoice' => $order->xendit_invoice_id,
                                'auto' => 1,
                            ]),
                            'xendit' => [
                                'invoice_id' => $order->xendit_invoice_id,
                                'invoice_url' => $order->xendit_invoice_url,
                            ],
                        ], 422);
                    }

                    return redirect()->route('checkout.payment', [
                        'order' => $order,
                        'invoice' => $order->xendit_invoice_id,
                        'auto' => 1,
                    ])->withErrors(__('Tidak dapat membuat transaksi pembayaran secara otomatis. Silakan coba lagi dari halaman pembayaran.'));
                }

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => __('Pesanan berhasil dibuat, namun pembayaran belum dapat diproses. Silakan hubungi kasir.'),
                    ], 422);
                }

                return redirect()->route('home')
                    ->withErrors(__('Pesanan berhasil dibuat, namun pembayaran belum dapat diproses. Silakan hubungi kasir.'));
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('Tidak dapat memproses pembayaran. Silakan coba kembali.'),
                ], 422);
            }

            return redirect()->route('cart.index')
                ->withErrors(__('Tidak dapat memproses pembayaran. Silakan coba kembali.'));
        }

        $this->finalizeCart($request, $cart);
        $this->rememberOrderForGuest($request, $order, $validated['phone'] ?? null);

        if (! $order->xendit_invoice_url) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('Pesanan berhasil dibuat, namun pembayaran belum dapat diproses. Silakan hubungi kasir.'),
                ], 422);
            }

            return redirect()->route('home')
                ->withErrors(__('Pesanan berhasil dibuat, namun pembayaran belum dapat diproses. Silakan hubungi kasir.'));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('Pesanan berhasil dibuat. Lanjutkan ke pembayaran.'),
                'order' => [
                    'id' => $order->getKey(),
                    'number' => $order->order_number,
                    'show_url' => route('customer.orders.show', $order),
                ],
                'xendit' => [
                    'invoice_id' => $order->xendit_invoice_id,
                    'invoice_url' => $order->xendit_invoice_url,
                    'result_urls' => [
                        'success' => route('xendit.success', ['orderNumber' => $order->order_number]),
                        'failed' => route('xendit.failed', ['orderNumber' => $order->order_number]),
                    ],
                ],
                'checkout_payment_url' => route('checkout.payment', [
                    'order' => $order,
                    'invoice' => $order->xendit_invoice_id,
                    'auto' => 1,
                ]),
            ]);
        }

        return redirect()->route('checkout.payment', [
            'order' => $order,
            'invoice' => $order->xendit_invoice_id,
            'auto' => 1,
        ])->with('status', __('Pesanan berhasil dibuat. Lanjutkan ke pembayaran.'));
    }

    public function payment(Order $order, string $invoice): View
    {
        abort_if($order->xendit_invoice_id !== $invoice || ! $order->xendit_invoice_url, 404);

        $order->load('items.menuItem');

        return view('customer.checkout.payment', [
            'order' => $order,
            'invoiceUrl' => $order->xendit_invoice_url,
        ]);
    }

    protected function finalizeCart(Request $request, Cart $cart): void
    {
        $cart->markAsSubmitted();
        $request->session()->forget('cart_token');
    }

    protected function rememberOrderForGuest(Request $request, Order $order, ?string $phone = null): void
    {
        try {
            $ids = collect((array) $request->session()->get('recent_orders', []));
            $ids = $ids->prepend($order->id)->unique()->take(10)->values();
            $request->session()->put('recent_orders', $ids->all());

            if ($phone) {
                $digits = preg_replace('/\D+/', '', $phone);
                if ($digits) {
                    $request->session()->put('order_lookup_phone', $digits);
                }
            }

            $request->session()->put('last_order_number', $order->order_number);
        } catch (\Throwable $e) {
            // ignore
        }
    }
}
