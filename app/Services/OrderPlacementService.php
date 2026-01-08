<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class OrderPlacementService
{
    /**
     * Place an order using cart items (preserving item options).
     */
    public function placeFromCart(
        Request $request,
        \App\Models\Cart $cart,
        ?string $notes,
        ?\App\Models\User $user,
        array $customerData = [],
        ?int $tableNumber = null,
    ): Order {
        $cart->load(['items.menuItem']);

        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages(['items' => 'Keranjang kosong.']);
        }

        return DB::transaction(function () use ($cart, $notes, $user, $customerData, $tableNumber) {
            $menuItems = $cart->items->pluck('menuItem')->filter()->keyBy('id');
            if ($menuItems->isEmpty()) {
                throw ValidationException::withMessages(['items' => 'Menu tidak ditemukan.']);
            }

            // Aggregate quantities per menu for stock validation
            $aggregated = $cart->items->groupBy('menu_item_id')->map(fn ($group) => (int) $group->sum('quantity'));

            foreach ($menuItems as $menuItem) {
                if (! $menuItem->is_active) {
                    throw ValidationException::withMessages(['items' => "Menu {$menuItem->name} tidak tersedia."]);
                }
                if ($menuItem->stock < ($aggregated[$menuItem->id] ?? 0)) {
                    throw ValidationException::withMessages(['items' => "Stok menu {$menuItem->name} tidak mencukupi."]);
                }
            }

            $baseData = [
                'user_id' => $user?->id,
                'customer_name' => $customerData['customer_name'] ?? $user?->name,
                'customer_email' => $customerData['customer_email'] ?? $user?->email,
                'customer_phone' => $customerData['customer_phone'] ?? $user?->phone,
                'status' => OrderStatus::Pending,
                'payment_status' => PaymentStatus::Unpaid,
                'notes' => $notes,
            ];
            // Graceful fallback if column not yet migrated
            if (Schema::hasColumn('orders', 'table_number')) {
                $baseData['table_number'] = $tableNumber;
            } else {
                if ($tableNumber) {
                    $baseData['notes'] = trim(($notes ? ($notes.'; ') : '').'No. Meja: '.$tableNumber);
                }
            }

            $order = Order::create($baseData);

            $total = 0;
            foreach ($cart->items as $item) {
                $subtotal = $item->unit_price * $item->quantity;
                $total += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item->menu_item_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $subtotal,
                    'notes' => $item->notes,
                ]);
            }

            // Decrement stock by aggregated quantities
            foreach ($menuItems as $menuItem) {
                $menuItem->stock -= ($aggregated[$menuItem->id] ?? 0);
                $menuItem->save();
            }

            $order->update(['total_amount' => $total]);

            return $order;
        });
    }

    /**
     * @param  array<int|string, mixed>  $quantities
     * @param  array{customer_name?: string|null, customer_email?: string|null, customer_phone?: string|null}  $customerData
     */
    public function place(
        Request $request,
        array $quantities,
        ?string $notes,
        ?User $user,
        array $customerData = [],
        ?int $tableNumber = null,
    ): Order {
        $items = collect($quantities)
            ->filter(fn ($quantity) => (int) $quantity > 0)
            ->map(fn ($quantity) => (int) $quantity);

        if ($items->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Pilih minimal satu menu untuk dipesan.',
            ]);
        }

        [$order, $lowStockItems] = $this->createOrderWithinTransaction(
            $items,
            $notes,
            $user,
            $customerData,
            $tableNumber
        );

        $this->notifyLowStock($lowStockItems);

        return $order;
    }

    /**
     * @return array{0: Order, 1: Collection<int, MenuItem>}
     */
    protected function createOrderWithinTransaction(
        Collection $items,
        ?string $notes,
        ?User $user,
        array $customerData,
        ?int $tableNumber = null
    ): array {
        return DB::transaction(function () use ($items, $notes, $user, $customerData, $tableNumber) {
            /** @var Collection<int, MenuItem> $menuItems */
            $menuItems = MenuItem::whereIn('id', $items->keys())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($menuItems->count() !== $items->count()) {
                throw ValidationException::withMessages([
                    'items' => 'Menu tidak ditemukan. Silakan ulangi pilihan.',
                ]);
            }

            foreach ($menuItems as $menuItem) {
                if (! $menuItem->is_active) {
                    throw ValidationException::withMessages([
                        'items' => "Menu {$menuItem->name} sedang tidak tersedia.",
                    ]);
                }

                if ($menuItem->stock < $items[$menuItem->id]) {
                    throw ValidationException::withMessages([
                        'items' => "Stok menu {$menuItem->name} tidak mencukupi.",
                    ]);
                }
            }

            $order = Order::create([
                'user_id' => $user?->id,
                'customer_name' => $customerData['customer_name'] ?? $user?->name,
                'customer_email' => $customerData['customer_email'] ?? $user?->email,
                'customer_phone' => $customerData['customer_phone'] ?? $user?->phone,
                'table_number' => $tableNumber,
                'status' => OrderStatus::Pending,
                'payment_status' => PaymentStatus::Unpaid,
                'notes' => $notes,
            ]);

            $total = 0;
            $lowStockAlerts = collect();

            foreach ($menuItems as $menuItem) {
                $quantity = $items[$menuItem->id];
                $subtotal = $menuItem->price * $quantity;
                $total += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $quantity,
                    'unit_price' => $menuItem->price,
                    'subtotal' => $subtotal,
                ]);

                $menuItem->stock -= $quantity;

                $shouldNotify = false;

                if ($menuItem->stock <= $menuItem->low_stock_threshold) {
                    if (! $menuItem->low_stock_notified_at) {
                        $menuItem->low_stock_notified_at = now();
                        $shouldNotify = true;
                    }
                } elseif ($menuItem->low_stock_notified_at) {
                    $menuItem->low_stock_notified_at = null;
                }

                $menuItem->save();

                if ($shouldNotify) {
                    $lowStockAlerts->push(clone $menuItem);
                }
            }

            $order->update([
                'total_amount' => $total,
            ]);

            return [$order, $lowStockAlerts];
        });
    }

    protected function notifyLowStock(Collection $lowStockItems): void
    {
        if ($lowStockItems->isEmpty()) {
            return;
        }

        $admins = User::where('role', UserRole::Admin)->get();

        if ($admins->isEmpty()) {
            return;
        }

        foreach ($lowStockItems as $menuItem) {
            Notification::send($admins, new LowStockNotification($menuItem, $menuItem->stock));
        }
    }

}
