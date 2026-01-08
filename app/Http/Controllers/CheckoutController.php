<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Cart;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderPlacementService;
use App\Services\QrisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private OrderPlacementService $orderPlacementService,
        private QrisService $qrisService,
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
        }

        $this->finalizeCart($request, $cart);
        $this->rememberOrderForGuest($request, $order, $validated['phone'] ?? null);

        $baseAmount = (int) round((float) $order->total_amount);
        $uniqueCode = 0;
        if ($baseAmount > 1) {
            $uniqueCode = random_int(1, max(1, min(999, $baseAmount - 1)));
        }
        $adjustedBase = max(0, $baseAmount - $uniqueCode);
        $payableAmount = $adjustedBase + $uniqueCode;
        $paymentPayload = array_merge((array) $order->payment_payload, [
            'base_amount' => $baseAmount,
            'adjusted_base_amount' => $adjustedBase,
            'unique_code' => $uniqueCode,
            'qris_amount' => $payableAmount,
        ]);

        $order->update([
            'total_amount' => $payableAmount,
            'payment_status' => PaymentStatus::Pending,
            'payment_payload' => $paymentPayload,
        ]);

        $qrisString = null;

        try {
            $qrisString = $this->qrisService->generateFromEnv($payableAmount);
            $this->cacheQrisString($request, $order, $qrisString);
        } catch (\Throwable $exception) {
            report($exception);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $qrisString
                    ? __('Pesanan berhasil dibuat. Lanjutkan ke pembayaran.')
                    : __('Pesanan berhasil dibuat, namun QRIS belum dapat dibuat otomatis. Silakan hubungi kasir.'),
                'order' => [
                    'id' => $order->getKey(),
                    'number' => $order->order_number,
                    'show_url' => route('customer.orders.show', $order),
                ],
                'payment' => [
                    'qris_string' => $qrisString,
                    'amount' => $payableAmount,
                    'unique_code' => $uniqueCode,
                ],
                'checkout_payment_url' => route('checkout.payment', [
                    'order' => $order->order_number,
                ]),
            ]);
        }

        if (! $qrisString) {
            return redirect()->route('home')
                ->withErrors(__('Pesanan berhasil dibuat, namun QRIS belum dapat dibuat otomatis. Silakan hubungi kasir.'));
        }

        return redirect()->route('checkout.payment', [
            'order' => $order->order_number,
        ])->with('status', __('Pesanan berhasil dibuat. Lanjutkan ke pembayaran.'));
    }

    public function payment(Request $request, Order $order): View
    {
        abort_unless($this->canAccessOrder($request, $order), 404);

        $order->load('items.menuItem');

        $payableAmount = (int) round((float) $order->total_amount);
        $cachedQris = $this->getCachedQrisString($request, $order);
        $qrisString = $cachedQris;

        if (! $qrisString) {
            try {
                $qrisString = $this->qrisService->generateFromEnv($payableAmount);
            } catch (\Throwable $exception) {
                report($exception);
                $qrisString = null;
            }
        }

        if ($qrisString && ! $cachedQris) {
            $this->cacheQrisString($request, $order, $qrisString);
        }

        return view('customer.checkout.payment', [
            'order' => $order,
            'qrisString' => $qrisString,
            'payableAmount' => $payableAmount,
            'uniqueCode' => $order->payment_payload['unique_code'] ?? null,
        ]);
    }

    public function confirmPayment(Request $request, Order $order): RedirectResponse
    {
        abort_unless($this->canAccessOrder($request, $order), 404);

        if ($order->payment_status === PaymentStatus::Paid) {
            return back()->with('status', __('Pembayaran sudah ditandai lunas. Terima kasih!'));
        }

        $payload = (array) $order->payment_payload;
        $payload['confirmation_requested_at'] = now()->toIso8601String();

        $order->update([
            'payment_status' => PaymentStatus::Pending,
            'payment_payload' => $payload,
        ]);

        $redirectUrl = $request->user()
            ? route('customer.orders.show', $order)
            : route('checkout.payment', ['order' => $order->order_number]);

        return redirect($redirectUrl)->with('status', __('Konfirmasi pembayaran sudah dikirim. Kami akan memverifikasi pembayaranmu secepatnya.'));
    }

    public function status(Request $request, Order $order): JsonResponse
    {
        abort_unless($this->canAccessOrder($request, $order), 404);

        return response()->json([
            'order_number' => $order->order_number,
            'payment_status' => $order->payment_status->value,
            'payment_status_label' => $order->payment_status->label(),
            'order_status' => $order->status->value,
            'paid_at' => $order->paid_at?->toIso8601String(),
            'total_amount' => (float) $order->total_amount,
        ]);
    }

    public function paid(Request $request, Order $order): View
    {
        abort_unless($this->canAccessOrder($request, $order), 404);

        $order->load('items.menuItem');

        return view('customer.checkout.paid', [
            'order' => $order,
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

    protected function cacheQrisString(Request $request, Order $order, string $qrisString): void
    {
        $request->session()->put($this->qrisSessionKey($order), $qrisString);
    }

    protected function getCachedQrisString(Request $request, Order $order): ?string
    {
        return $request->session()->get($this->qrisSessionKey($order));
    }

    protected function qrisSessionKey(Order $order): string
    {
        $static = (string) (config('qris.static_qris') ?? env('SHOP_STATIC_QRIS'));
        $amountHash = substr(hash('crc32b', (string) $order->total_amount), 0, 8);
        $staticHash = substr(hash('crc32b', $static), 0, 8);

        return 'qris.order.'.$order->getKey().'.'.$staticHash.'.'.$amountHash;
    }

    protected function canAccessOrder(Request $request, Order $order): bool
    {
        if ($request->user() && $request->user()->id === $order->user_id) {
            return true;
        }

        $recentIds = (array) $request->session()->get('recent_orders', []);
        if (in_array($order->id, $recentIds, true)) {
            return true;
        }

        return $request->session()->get('last_order_number') === $order->order_number;
    }
}
