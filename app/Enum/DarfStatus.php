<?php

namespace App\Enum;

enum DarfStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case CANCELED = 'canceled';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => __('Pending'),
            self::PAID => __('Paid'),
            self::CANCELED => __('Canceled'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PAID => 'positive',
            self::CANCELED => 'negative',
        };
    }
}
