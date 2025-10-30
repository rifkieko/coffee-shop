<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Exceptions\PaymentException;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShopTable;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Throwable;

class OrderPlacementService
{
    public function __construct(private MidtransService $midtransService)
    {
    }

    /**
     * @param  array<int|string, mixed>  $quantities
     * @param  array{customer_name?: string|null, customer_email?: string|null, customer_phone?: string|null}  $customerData
     */
    public function place(
        Request $request,
        array $quantities,
        ?ShopTable $table,
        ?string $notes,
        ?User $user,
        array $customerData = []
    ): Order {
        $items = collect($quantities)
            ->filter(fn ($quantity) => (int) $quantity > 0)
            ->map(fn ($quantity) => (int) $quantity);

        if ($items->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Pilih minimal satu menu untuk dipesan.',
            ]);
        }

        [$order, $menuItems, $lowStockItems] = $this->createOrderWithinTransaction(
            $items,
            $table,
            $notes,
            $user,
            $customerData
        );

        $this->notifyLowStock($lowStockItems);

        $this->attachMidtransTransaction($request, $order, $menuItems, $items, $user, $customerData);

        return $order;
    }

    /**
     * @return array{0: Order, 1: Collection<int, MenuItem>, 2: Collection<int, MenuItem>}
     */
    protected function createOrderWithinTransaction(
        Collection $items,
        ?ShopTable $table,
        ?string $notes,
        ?User $user,
        array $customerData
    ): array {
        return DB::transaction(function () use ($items, $table, $notes, $user, $customerData) {
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
                'shop_table_id' => $table?->id,
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

            return [$order, $menuItems->values(), $lowStockAlerts];
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

    protected function attachMidtransTransaction(
        Request $request,
        Order $order,
        Collection $menuItems,
        Collection $quantities,
        ?User $user,
        array $customerData
    ): void {
        try {
            $payload = $this->buildMidtransPayload($order, $menuItems, $quantities, $user, $customerData, $request);

            $transaction = $this->midtransService->createTransaction($payload);

            $order->update([
                'midtrans_order_id' => $payload['transaction_details']['order_id'] ?? null,
                'midtrans_token' => $transaction['token'] ?? null,
                'midtrans_redirect_url' => $transaction['redirect_url'] ?? null,
                'payment_status' => PaymentStatus::Pending,
                'payment_payload' => $transaction,
            ]);
        } catch (Throwable $exception) {
            throw new PaymentException('Tidak dapat membuat transaksi pembayaran.', 0, $exception, $order);
        }
    }

    protected function buildMidtransPayload(
        Order $order,
        Collection $menuItems,
        Collection $quantities,
        ?User $user,
        array $customerData,
        Request $request
    ): array {
        $customerName = $customerData['customer_name'] ?? $user?->name;
        $customerEmail = $customerData['customer_email'] ?? $user?->email;
        $customerPhone = $customerData['customer_phone'] ?? $user?->phone;

        return [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) round($order->total_amount),
            ],
            'customer_details' => [
                'first_name' => $customerName,
                'email' => $customerEmail,
                'phone' => $customerPhone,
            ],
            'item_details' => $menuItems->map(function (MenuItem $menuItem) use ($quantities) {
                return [
                    'id' => (string) $menuItem->id,
                    'price' => (int) round($menuItem->price),
                    'quantity' => $quantities[$menuItem->id] ?? 1,
                    'name' => $menuItem->name,
                ];
            })->values()->all(),
            'enabled_payments' => ['qris'],
            'callbacks' => [
                'finish' => config('midtrans.callbacks.finish'),
                'error' => config('midtrans.callbacks.error'),
                'notification' => config('midtrans.callbacks.notification'),
                'unfinish' => config('midtrans.callbacks.unfinish'),
            ],
        ];
    }
}
