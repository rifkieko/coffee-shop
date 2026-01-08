<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'menu_item_id',
        'menu_name',
        'quantity',
        'unit_price',
        'subtotal',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $item): void {
            if ($item->unit_price === null && $item->menuItem) {
                $item->unit_price = $item->menuItem->price;
            }

            // Persist menu name snapshot to keep report history even if menu changes later.
            if ($item->menu_name === null && $item->menuItem) {
                $item->menu_name = $item->menuItem->name;
            }

            $item->subtotal = $item->quantity * $item->unit_price;
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
