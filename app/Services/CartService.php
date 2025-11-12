<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartService
{
    public function getActiveCart(Request $request): Cart
    {
        $sessionToken = $request->session()->get('cart_token');
        $user = $request->user();

        $sessionCart = $sessionToken
            ? Cart::where('status', Cart::STATUS_ACTIVE)
                ->where('session_token', $sessionToken)
                ->with('items.menuItem.category')
                ->first()
            : null;

        $userCart = $user
            ? Cart::where('status', Cart::STATUS_ACTIVE)
                ->where('user_id', $user->id)
                ->with('items.menuItem.category')
                ->first()
            : null;

        if ($user && $userCart && $sessionCart && $userCart->id !== $sessionCart->id) {
            $this->mergeCarts($userCart, $sessionCart);
            $sessionCart = null;
        }

        if ($user && $userCart) {
            $this->ensureSessionToken($request, $userCart);

            return $userCart->load('items.menuItem.category');
        }

        if ($sessionCart) {
            if ($user && ! $sessionCart->user_id) {
                $sessionCart->update(['user_id' => $user->id]);
            }

            $this->ensureSessionToken($request, $sessionCart);

            return $sessionCart->load('items.menuItem.category');
        }

        $token = $sessionToken ?? $this->generateSessionToken($request);

        $cart = Cart::create([
            'user_id' => $user?->id,
            'session_token' => $token,
            'status' => Cart::STATUS_ACTIVE,
        ]);

        return $cart->load('items.menuItem.category');
    }

    protected function mergeCarts(Cart $destination, Cart $source): void
    {
        $source->load('items.menuItem');

        $source->items->each(function ($item) use ($destination): void {
            /** @var \App\Models\CartItem $item */
            $menuItem = $item->menuItem;

            if ($menuItem instanceof MenuItem) {
                $destination->addOrIncrementItem(
                    $menuItem,
                    $item->quantity,
                    $item->notes,
                    $item->temperature,
                    $item->sugar_level,
                    $item->ice_level,
                    $item->size,
                    $item->beans,
                    $item->milk_option,
                );
            }
        });

        $destination->recalculateTotals();

        $source->delete();
    }

    protected function ensureSessionToken(Request $request, Cart $cart): string
    {
        $token = $request->session()->get('cart_token');

        if (! $token || ($cart->session_token && $cart->session_token !== $token)) {
            $token = $cart->session_token ?? $this->generateSessionToken($request);
            $request->session()->put('cart_token', $token);
        }

        if (! $cart->session_token) {
            $cart->update(['session_token' => $token]);
        }

        return $token;
    }

    protected function generateSessionToken(Request $request): string
    {
        $token = (string) Str::uuid();
        $request->session()->put('cart_token', $token);

        return $token;
    }
}
