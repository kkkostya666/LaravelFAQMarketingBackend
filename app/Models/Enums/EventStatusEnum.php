<?php

namespace App\Models\Enums;

enum EventStatusEnum: int
{
    case Active = 1;
    case Inactive = 0;

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Актуально',
            self::Inactive => 'Неактуально',
        };
    }
}
