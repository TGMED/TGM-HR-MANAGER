<?php

namespace App\Services;

use App\Enums\AttemptResult;
use App\Models\Attendance;
use App\Models\ClockAttempt;

final readonly class ClockResult
{
    public function __construct(
        public AttemptResult $result,
        public string $message,
        public ClockAttempt $attempt,
        public ?Attendance $attendance = null,
    ) {}

    public function successful(): bool
    {
        return $this->result->isSuccess();
    }
}
