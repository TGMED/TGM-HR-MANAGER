<?php

namespace Database\Seeders;

use App\Enums\AttemptResult;
use App\Enums\AttendanceStatus;
use App\Enums\ClockType;
use App\Models\Attendance;
use App\Models\ClockAttempt;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $locations = $this->seedLocations();

        // Admins run the clock rather than punch it, so this one gets no
        // attendance of its own — only a base office for display.
        User::factory()->superAdmin()->create([
            'employee_id' => 'TGM-0001',
            'name' => 'Chidi Okafor',
            'email' => 'admin@tgm.test',
            'password' => 'password',
            'location_id' => $locations['lagos']->id,
        ]);

        // Spread staff across the branches so the per-location reports have shape.
        $staff = collect();

        foreach ([
            ['lagos', 5],
            ['abuja', 3],
            ['kano', 2],
            ['benin', 2],
            ['ibadan', 2],
            ['port-harcourt', 2],
            ['ghana', 2],
            ['uganda', 2],
        ] as [$key, $count]) {
            $staff = $staff->merge(
                User::factory($count)->create(['location_id' => $locations[$key]->id]),
            );
        }

        User::factory(2)->deactivated()->create(['location_id' => $locations['lagos']->id]);

        $this->seedAttendance($staff, $locations);
    }

    /**
     * The eight TGM Education branches.
     *
     * Addresses are taken verbatim from the branch address sheet. The
     * coordinates are district-level approximations, NOT surveyed to the
     * building — an administrator must drag each pin onto the actual door on
     * the Locations page before staff rely on the geofence. The radius is set
     * generously for the same reason.
     *
     * Opening hours were not in the source document, so every branch starts on
     * the same default and can be tuned per site in the UI.
     *
     * @return array<string, Location>
     */
    protected function seedLocations(): array
    {
        $branches = [
            'lagos' => [
                'name' => 'TGM Lagos (Head Office)',
                'address' => '#46 Harold Sodipo Crescent, Ikeja GRA, Ikeja',
                'city' => 'Lagos',
                'latitude' => 6.5796,
                'longitude' => 3.3521,
                'timezone' => 'Africa/Lagos',
            ],
            'abuja' => [
                'name' => 'TGM Abuja',
                'address' => 'Suite 313 GCL Plaza, 522 Aminu Kano Crescent, Wuse 2, Opp. DBM Plaza',
                'city' => 'Abuja',
                'latitude' => 9.0765,
                'longitude' => 7.4740,
                'timezone' => 'Africa/Lagos',
            ],
            'kano' => [
                'name' => 'TGM Kano',
                'address' => 'Shop B8 Turai Plaza (Beside 9mobile Office), Audu Bako Way, Nasarawa GRA',
                'city' => 'Kano',
                'latitude' => 11.9986,
                'longitude' => 8.5266,
                'timezone' => 'Africa/Lagos',
            ],
            'benin' => [
                'name' => 'TGM Benin',
                'address' => '2nd Floor (Asimowu House), 44 Akpakpava Road',
                'city' => 'Benin City',
                'latitude' => 6.3405,
                'longitude' => 5.6280,
                'timezone' => 'Africa/Lagos',
            ],
            'ibadan' => [
                'name' => 'TGM Ibadan',
                'address' => '47 Along Liberty Road, Oke-Ado',
                'city' => 'Ibadan',
                'latitude' => 7.3725,
                'longitude' => 3.8836,
                'timezone' => 'Africa/Lagos',
            ],
            'port-harcourt' => [
                'name' => 'TGM Port Harcourt',
                'address' => 'First Floor, Nextime Supermarket Building, Lenu Plaza, Phase 2 Abacha Road, New GRA',
                'city' => 'Port Harcourt',
                'latitude' => 4.8196,
                'longitude' => 7.0135,
                'timezone' => 'Africa/Lagos',
            ],
            // Accra runs on GMT and Kampala on EAT, so these two are judged
            // against a different wall clock to the Nigerian branches.
            'ghana' => [
                'name' => 'TGM Ghana',
                'address' => '34 Lagos Avenue, GCB Building, East Legon',
                'city' => 'Accra',
                'latitude' => 5.6363,
                'longitude' => -0.1637,
                'timezone' => 'Africa/Accra',
            ],
            'uganda' => [
                'name' => 'TGM Uganda',
                'address' => 'The Cube (Opp. Acacia Mall), Copper Rd',
                'city' => 'Kampala',
                'latitude' => 0.3343,
                'longitude' => 32.5893,
                'timezone' => 'Africa/Kampala',
            ],
        ];

        $created = [];

        foreach ($branches as $key => $attributes) {
            $created[$key] = Location::query()->create([
                ...$attributes,
                'radius_meters' => 250,
                'max_accuracy_meters' => 200,
                'work_starts_at' => '08:00:00',
                'work_ends_at' => '17:00:00',
                'grace_minutes' => 10,
                'workdays' => [1, 2, 3, 4, 5],
                'is_active' => true,
                'accepts_signups' => true,
            ]);
        }

        return $created;
    }

    /**
     * Six weeks of plausible punches, including a few rejected attempts.
     *
     * @param  Collection<int, User>  $users
     * @param  array<string, Location>  $locations
     */
    protected function seedAttendance(Collection $users, array $locations): void
    {
        $byId = collect($locations)->keyBy('id');

        foreach ($users as $user) {
            /** @var Location|null $location */
            $location = $byId->get($user->location_id);

            if ($location === null) {
                continue;
            }

            $today = Carbon::now()->setTimezone($location->timezone);

            for ($daysAgo = 41; $daysAgo >= 0; $daysAgo--) {
                $date = $today->copy()->subDays($daysAgo);

                if (! $location->isWorkday($date)) {
                    continue;
                }

                // Roughly one absence every couple of weeks.
                if (random_int(1, 100) <= 8) {
                    continue;
                }

                $inMinutes = random_int(1, 100) <= 25
                    ? random_int(12, 75)   // late arrival
                    : random_int(-35, 8);  // on time or early

                $clockIn = $location->startOfWorkFor($date)->addMinutes($inMinutes);

                $isToday = $daysAgo === 0;

                if ($isToday && $clockIn->greaterThan($today)) {
                    continue;
                }

                $late = $inMinutes > $location->grace_minutes;
                $distance = random_int(5, (int) ($location->radius_meters * 0.9));

                $clockOut = $isToday && random_int(1, 100) <= 70
                    ? null
                    : $clockIn->copy()->addMinutes(random_int(430, 560));

                if ($clockOut !== null && $clockOut->greaterThan($today)) {
                    $clockOut = null;
                }

                $attendance = Attendance::query()->create([
                    'user_id' => $user->id,
                    'location_id' => $location->id,
                    'work_date' => $date->toDateString(),
                    'clocked_in_at' => $clockIn->copy()->utc(),
                    'clock_in_latitude' => $this->jitter($location->latitude),
                    'clock_in_longitude' => $this->jitter($location->longitude),
                    'clock_in_accuracy' => random_int(8, 60),
                    'clock_in_distance' => $distance,
                    'clocked_out_at' => $clockOut?->copy()->utc(),
                    'clock_out_latitude' => $clockOut ? $this->jitter($location->latitude) : null,
                    'clock_out_longitude' => $clockOut ? $this->jitter($location->longitude) : null,
                    'clock_out_accuracy' => $clockOut ? random_int(8, 60) : null,
                    'clock_out_distance' => $clockOut ? random_int(5, 140) : null,
                    'status' => $late ? AttendanceStatus::Late : AttendanceStatus::OnTime,
                    'late_minutes' => $late ? $inMinutes : 0,
                    'worked_minutes' => $clockOut ? (int) $clockIn->diffInMinutes($clockOut) : null,
                ]);

                // Some days start with a rejected attempt from outside the fence.
                if (random_int(1, 100) <= 15) {
                    $rejectedDistance = random_int($location->radius_meters + 60, 4200);
                    $at = $clockIn->copy()->subMinutes(random_int(4, 30));

                    ClockAttempt::query()->create([
                        'user_id' => $user->id,
                        'location_id' => $location->id,
                        'type' => ClockType::In,
                        'result' => AttemptResult::OutOfRange,
                        'message' => "You are {$rejectedDistance}m from {$location->name}.",
                        'latitude' => $this->jitter($location->latitude, 0.03),
                        'longitude' => $this->jitter($location->longitude, 0.03),
                        'accuracy_meters' => random_int(10, 90),
                        'distance_meters' => $rejectedDistance,
                        'ip_address' => '105.112.'.random_int(1, 254).'.'.random_int(1, 254),
                        'user_agent' => 'Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 Chrome/126 Mobile Safari/537.36',
                        'created_at' => $at->utc(),
                        'updated_at' => $at->utc(),
                    ]);
                }

                ClockAttempt::query()->create([
                    'user_id' => $user->id,
                    'location_id' => $location->id,
                    'attendance_id' => $attendance->id,
                    'type' => ClockType::In,
                    'result' => AttemptResult::Success,
                    'message' => 'Clocked in at '.$clockIn->format('g:i A').'.',
                    'latitude' => $attendance->clock_in_latitude,
                    'longitude' => $attendance->clock_in_longitude,
                    'accuracy_meters' => $attendance->clock_in_accuracy,
                    'distance_meters' => $distance,
                    'ip_address' => '105.112.'.random_int(1, 254).'.'.random_int(1, 254),
                    'user_agent' => 'Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 Chrome/126 Mobile Safari/537.36',
                    'created_at' => $clockIn->copy()->utc(),
                    'updated_at' => $clockIn->copy()->utc(),
                ]);
            }
        }
    }

    protected function jitter(float $value, float $spread = 0.0009): float
    {
        return round($value + (random_int(-1000, 1000) / 1000) * $spread, 7);
    }
}
