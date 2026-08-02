<?php

namespace App\Services;

use Illuminate\Http\Request;

/**
 * The raw geolocation reading and request metadata submitted with a punch.
 */
final readonly class ClockPunch
{
    public function __construct(
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?int $accuracy = null,
        public ?string $ipAddress = null,
        public ?string $userAgent = null,
    ) {}

    /**
     * @param  array{latitude?: float|null, longitude?: float|null, accuracy?: int|float|null}  $data
     */
    public static function fromRequest(Request $request, array $data): self
    {
        return new self(
            latitude: isset($data['latitude']) ? (float) $data['latitude'] : null,
            longitude: isset($data['longitude']) ? (float) $data['longitude'] : null,
            accuracy: isset($data['accuracy']) ? (int) round((float) $data['accuracy']) : null,
            ipAddress: $request->ip(),
            userAgent: substr((string) $request->userAgent(), 0, 1000),
        );
    }

    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }
}
