<?php

namespace App\Http\Controllers;

use App\Enums\ClockType;
use App\Services\ClockPunch;
use App\Services\ClockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClockController extends Controller
{
    public function __construct(protected ClockService $clock) {}

    public function store(Request $request, string $type): RedirectResponse
    {
        $clockType = ClockType::tryFrom($type);

        abort_if($clockType === null, 404);

        $data = $request->validate([
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0', 'max:100000'],
        ]);

        $punch = ClockPunch::fromRequest($request, $data);

        $result = $clockType === ClockType::In
            ? $this->clock->clockIn($request->user(), $punch)
            : $this->clock->clockOut($request->user(), $punch);

        return back()->with('clock', [
            'ok' => $result->successful(),
            'result' => $result->result->value,
            'label' => $result->result->label(),
            'message' => $result->message,
            'distance_meters' => $result->attempt->distance_meters,
        ]);
    }
}
