<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShopTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'slug',
        'qr_token',
        'capacity',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $table): void {
            $table->code ??= strtoupper(Str::random(6));
            $table->slug ??= Str::slug($table->name.'-'.$table->code);
            $table->qr_token ??= Str::uuid()->toString();
        });
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getOrderUrlAttribute(): string
    {
        return route('home', absolute: true);
    }

    public function getTableOrderUrlAttribute(): string
    {
        return route('customer.orders.create', ['table' => $this->slug], absolute: true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
