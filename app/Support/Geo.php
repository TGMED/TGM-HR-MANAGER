<?php

namespace App\Support;

final class Geo
{
    private const EARTH_RADIUS_METERS = 6371000.0;

    /**
     * Great-circle distance between two coordinates, in whole metres.
     */
    public static function distanceInMeters(
        float $fromLatitude,
        float $fromLongitude,
        float $toLatitude,
        float $toLongitude,
    ): int {
        $latitudeDelta = deg2rad($toLatitude - $fromLatitude);
        $longitudeDelta = deg2rad($toLongitude - $fromLongitude);

        $a = sin($latitudeDelta / 2) ** 2
            + cos(deg2rad($fromLatitude))
            * cos(deg2rad($toLatitude))
            * sin($longitudeDelta / 2) ** 2;

        return (int) round(
            self::EARTH_RADIUS_METERS * 2 * atan2(sqrt($a), sqrt(1 - $a)),
        );
    }
}
