<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Preparing = 'preparing';
    case Served = 'served';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Pending => 'Pending',
            self::Preparing => 'Preparing',
            self::Served => 'Served',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }
}

