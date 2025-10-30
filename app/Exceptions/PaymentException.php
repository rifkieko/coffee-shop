<?php

namespace App\Exceptions;

use App\Models\Order;
use RuntimeException;
use Throwable;

class PaymentException extends RuntimeException
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        private ?Order $order = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function order(): ?Order
    {
        return $this->order;
    }
}
