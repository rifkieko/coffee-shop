<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'low_stock_threshold',
        'low_stock_notified_at',
        'is_active',
        'image_path',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'low_stock_threshold' => 'integer',
        'low_stock_notified_at' => 'datetime',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function reduceStock(int $quantity): void
    {
        $this->stock -= $quantity;
        $this->save();
    }

    public function needsLowStockNotification(): bool
    {
        return $this->stock <= $this->low_stock_threshold;
    }

    public function shouldResetLowStockNotification(): bool
    {
        return $this->stock > $this->low_stock_threshold && $this->low_stock_notified_at !== null;
    }

    public function markLowStockNotified(): void
    {
        $this->forceFill([
            'low_stock_notified_at' => now(),
        ])->save();
    }

    public function clearLowStockNotification(): void
    {
        $this->forceFill([
            'low_stock_notified_at' => null,
        ])->save();
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }
}
