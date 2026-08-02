<?php

namespace App\Enums;

enum ClockType: string
{
    case In = 'in';
    case Out = 'out';

    public function label(): string
    {
        return match ($this) {
            self::In => 'Clock in',
            self::Out => 'Clock out',
        };
    }
}
