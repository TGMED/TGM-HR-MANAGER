<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case OnTime = 'on_time';
    case Late = 'late';

    public function label(): string
    {
        return match ($this) {
            self::OnTime => 'On time',
            self::Late => 'Late',
        };
    }
}
