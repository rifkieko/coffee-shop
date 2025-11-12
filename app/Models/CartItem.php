<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'menu_item_id',
        'quantity',
        'unit_price',
        'temperature',
        'sugar_level',
        'ice_level',
        'size',
        'beans',
        'milk_option',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'sugar_level' => 'integer',
        'ice_level' => 'integer',
    ];

    protected $appends = ['subtotal'];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) ($this->quantity * $this->unit_price);
    }
}
