<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkLocationController extends Controller
{
    /**
     * Let a staff member claim their site when they do not have one yet.
     *
     * This is deliberately one-way: once a site is set, only an administrator
     * can move them. Otherwise anyone could switch to whichever office they
     * happen to be standing next to and walk straight through the geofence.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_if(
            $user->location_id !== null,
            403,
            'Your work location can only be changed by an administrator.',
        );

        $validated = $request->validate([
            'location_id' => [
                'required',
                'integer',
                Rule::exists('locations', 'id')
                    ->where('is_active', true)
                    ->where('accepts_signups', true),
            ],
        ], [
            'location_id.required' => 'Choose the site you clock in at.',
            'location_id.exists' => 'That site is not open to new staff.',
        ]);

        $user->update(['location_id' => $validated['location_id']]);

        $location = Location::query()
            ->whereKey($validated['location_id'])
            ->firstOrFail();

        return back()->with('toast', [
            'type' => 'success',
            'message' => "You are now based at {$location->name}. You can clock in from there.",
        ]);
    }
}
