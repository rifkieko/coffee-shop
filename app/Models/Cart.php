<?php

namespace App\Models;

use App\Models\CartItem;
use App\Models\MenuItem;
use App\Models\ShopTable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_token',
        'shop_table_id',
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

    public function table()
    {
        return $this->belongsTo(ShopTable::class, 'shop_table_id');
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function addOrIncrementItem(MenuItem $menuItem, int $quantity = 1, ?string $notes = null, ?string $temperature = null, ?int $sugarLevel = null, ?int $iceLevel = null, ?string $size = null, ?string $beans = null, ?string $milkOption = null): CartItem
    {
        $query = $this->items()->where('menu_item_id', $menuItem->id);
        if ($temperature !== null) { $query->where('temperature', $temperature); }
        else { $query->whereNull('temperature'); }
        if ($sugarLevel !== null) { $query->where('sugar_level', $sugarLevel); }
        else { $query->whereNull('sugar_level'); }
        if ($iceLevel !== null) { $query->where('ice_level', $iceLevel); }
        else { $query->whereNull('ice_level'); }
        if ($size !== null) { $query->where('size', $size); }
        else { $query->whereNull('size'); }
        if ($beans !== null) { $query->where('beans', $beans); }
        else { $query->whereNull('beans'); }
        if ($milkOption !== null) { $query->where('milk_option', $milkOption); }
        else { $query->whereNull('milk_option'); }

        $cartItem = $query->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
            if ($notes) {
                $cartItem->notes = $notes;
                $cartItem->save();
            }
        } else {
            $cartItem = $this->items()->create([
                'menu_item_id' => $menuItem->id,
                'quantity' => $quantity,
                'unit_price' => $menuItem->price,
                'temperature' => $temperature,
                'sugar_level' => $sugarLevel,
                'ice_level' => $iceLevel,
                'temperature' => $temperature,
                'sugar_level' => $sugarLevel,
                'ice_level' => $iceLevel,
                'size' => $size,
                'beans' => $beans,
                'milk_option' => $milkOption,
                'notes' => $notes,
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
