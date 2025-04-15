<?php

namespace App\Models\Enums;

use InvalidArgumentException;


enum EventEnum: int
{
    case Offline = 1;
    case Online = 2;

    public static function fromValue(int $value): self {
        return match ($value) {
            1 => self::Offline,
            2 => self::Online,
            default => throw new InvalidArgumentException('Invalid role value'),
        };
    }

    public function label(): string {
        return match ($this) {
            self::Offline => 'Оффлайн',
            self::Online => 'Онлайн',
        };
    }
}
