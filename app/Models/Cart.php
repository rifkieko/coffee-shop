<?php

namespace App\Models;

use App\Models\CartItem;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_token',
        'status',
        'subtotal',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
    ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUBMITTED = 'submitted';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function addOrIncrementItem(MenuItem $menuItem, int $quantity = 1, ?string $notes = null): CartItem
    {
        $normalizedNotes = $notes ? trim($notes) : null;
        if ($normalizedNotes === '') {
            $normalizedNotes = null;
        }

        $query = $this->items()
            ->where('menu_item_id', $menuItem->id);

        if ($normalizedNotes === null) {
            $query->whereNull('notes');
        } else {
            $query->where('notes', $normalizedNotes);
        }

        $cartItem = $query->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
            if ($normalizedNotes) {
                $cartItem->notes = $normalizedNotes;
                $cartItem->save();
            }
        } else {
            $cartItem = $this->items()->create([
                'menu_item_id' => $menuItem->id,
                'quantity' => $quantity,
                'unit_price' => $menuItem->price,
                'notes' => $normalizedNotes,
            ]);
        }

        $this->recalculateTotals();

        return $cartItem;
    }

    public function updateItemQuantity(CartItem $item, int $quantity): void
    {
        $item->update(['quantity' => $quantity]);
        $this->recalculateTotals();
    }

    public function removeItem(CartItem $item): void
    {
        $item->delete();
        $this->recalculateTotals();
    }

    public function clear(): void
    {
        $this->items()->delete();
        $this->update(['subtotal' => 0]);
    }

    public function recalculateTotals(): void
    {
        $subtotal = $this->items()->sum(DB::raw('quantity * unit_price'));
        $this->update(['subtotal' => $subtotal]);
    }

    public function markAsSubmitted(): void
    {
        $this->update([
            'status' => self::STATUS_SUBMITTED,
            'subtotal' => 0,
        ]);
        $this->items()->delete();
    }
}
