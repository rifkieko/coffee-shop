<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shop_table_id',
        'table_number',
        'status',
        'payment_status',
        'total_amount',
        'paid_amount',
        'paid_at',
        'midtrans_order_id',
        'midtrans_token',
        'midtrans_redirect_url',
        'payment_payload',
        'notes',
        'expires_at',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'payment_status' => PaymentStatus::class,
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
        'payment_payload' => 'array',
        'table_number' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $order): void {
            $order->order_number ??= 'ORD-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4));
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(ShopTable::class, 'shop_table_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', PaymentStatus::Paid);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [OrderStatus::Cancelled, OrderStatus::Completed]);
    }

    public function markAsPaid(float $amount, array $payload = []): void
    {
        $this->update([
            'payment_status' => PaymentStatus::Paid,
            'status' => OrderStatus::Completed,
            'paid_amount' => $amount,
            'paid_at' => now(),
            'payment_payload' => $payload,
        ]);
    }
}
