<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\MenuItem;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private CartService $cartService)
    {
    }

    public function index(Request $request): View
    {
        $cart = $this->cartService->getActiveCart($request);

        return view('customer.cart.index', compact('cart'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'menu_item_id' => ['required', 'exists:menu_items,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
            'redirect_to' => ['nullable', 'url'],
        ]);

        $redirectTo = $validated['redirect_to'] ?? null;
        unset($validated['redirect_to']);

        $menuItem = MenuItem::active()->inStock()->findOrFail($validated['menu_item_id']);

        if ($menuItem->stock < $validated['quantity']) {
            return back()->withErrors(__('Stok menu :name tidak mencukupi.', ['name' => $menuItem->name]));
        }

        $cart = $this->cartService->getActiveCart($request);
        $cart->addOrIncrementItem(
            $menuItem,
            $validated['quantity'],
            $validated['notes'] ?? null,
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('Menu ditambahkan ke keranjang.'),
                'variant' => 'success',
                'subtotal' => $cart->subtotal,
                'redirect' => $redirectTo,
            ]);
        }

        return $redirectTo
            ? redirect()->to($redirectTo)->with('status', __('Menu ditambahkan ke keranjang.'))
            : redirect()->route('home')->with('status', __('Menu ditambahkan ke keranjang.'));
    }

    public function update(Request $request, CartItem $item)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $cart = $this->cartService->getActiveCart($request);
        abort_unless($item->cart_id === $cart->id, 403);

        if ($item->menuItem->stock < $validated['quantity']) {
            return back()->withErrors(__('Stok menu :name tidak mencukupi.', ['name' => $item->menuItem->name]));
        }

        $item->update([
            'quantity' => $validated['quantity'],
            'notes' => $validated['notes'] ?? $item->notes,
        ]);

        $cart->recalculateTotals();

        return $this->respondWithCart($request, $cart, __('Keranjang diperbarui.'), 'success');
    }

    public function destroy(Request $request, CartItem $item)
    {
        $cart = $this->cartService->getActiveCart($request);
        abort_unless($item->cart_id === $cart->id, 403);

        $cart->removeItem($item);

        return $this->respondWithCart($request, $cart, __('Menu dihapus dari keranjang.'), 'error');
    }

    public function clear(Request $request)
    {
        $cart = $this->cartService->getActiveCart($request);
        $cart->clear();

        return $this->respondWithCart($request, $cart, __('Keranjang dikosongkan.'), 'success');
    }

    protected function respondWithCart(Request $request, Cart $cart, string $message, string $variant = 'success'): RedirectResponse|JsonResponse
    {
        $cart = $cart->fresh(['items.menuItem.category']);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'variant' => $variant,
                'html' => view('customer.cart.partials.items', ['cart' => $cart])->render(),
                'subtotal' => $cart->subtotal,
            ]);
        }

        return back()->with('status', $message);
    }

    public function summary(Request $request): JsonResponse
    {
        $cart = $this->cartService->getActiveCart($request)->fresh('items');
        return response()->json([
            'subtotal' => $cart->subtotal,
            'count' => (int) $cart->items->sum('quantity'),
        ]);
    }
}
