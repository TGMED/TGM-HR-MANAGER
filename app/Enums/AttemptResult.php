<?php

namespace App\Enums;

enum AttemptResult: string
{
    case Success = 'success';
    case OutOfRange = 'out_of_range';
    case NoLocation = 'no_location';
    case LowAccuracy = 'low_accuracy';
    case Duplicate = 'duplicate';
    case NotClockedIn = 'not_clocked_in';
    case NoLocationConfigured = 'no_location_configured';
    case NoLocationAssigned = 'no_location_assigned';

    public function label(): string
    {
        return match ($this) {
            self::Success => 'Success',
            self::OutOfRange => 'Outside geofence',
            self::NoLocation => 'Location unavailable',
            self::LowAccuracy => 'GPS accuracy too low',
            self::Duplicate => 'Already clocked in',
            self::NotClockedIn => 'Not clocked in',
            self::NoLocationConfigured => 'Site has no coordinates',
            self::NoLocationAssigned => 'No work location assigned',
        };
    }

    public function isSuccess(): bool
    {
        return $this === self::Success;
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $result): array => ['value' => $result->value, 'label' => $result->label()],
            self::cases(),
        );
    }
}
