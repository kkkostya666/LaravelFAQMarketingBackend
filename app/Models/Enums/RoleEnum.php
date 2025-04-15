<?php

namespace App\Models\Enums;

use InvalidArgumentException;


enum RoleEnum: int
{
    case Administrator = 1;
    case User = 2;

    public static function fromValue(int $value): self {
        return match ($value) {
            1 => self::Administrator,
            3 => self::User,
            default => throw new InvalidArgumentException('Invalid role value'),
        };
    }

    public function label(): string {
        return match ($this) {
            self::Administrator => 'Администрация',
            self::User => 'Пользователь',
        };
    }
}
