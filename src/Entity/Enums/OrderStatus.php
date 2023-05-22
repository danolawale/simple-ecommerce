<?php

declare(strict_types=1);

namespace App\Entity\Enums;

enum OrderStatus: string
{
    case STARTED = '1';
    case COMPLETED = '2';
    case CANCELLED = '3';
    public function toString(): string
    {
        return match ($this) {
            self::STARTED => 'started',
            self::COMPLETED => 'completed',
            self::CANCELLED => 'cancelled'
        };
    }
}
