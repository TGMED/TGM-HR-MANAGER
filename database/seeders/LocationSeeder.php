<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->seed();
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
     * Keyed on name so re-running this on a live database updates the branches
     * in place rather than duplicating them.
     *
     * @return array<string, Location>
     */
    public function seed(): array
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
            $created[$key] = Location::query()->updateOrCreate(
                ['name' => $attributes['name']],
                [
                    ...$attributes,
                    'radius_meters' => 250,
                    'max_accuracy_meters' => 200,
                    'work_starts_at' => '08:00:00',
                    'work_ends_at' => '17:00:00',
                    'grace_minutes' => 10,
                    'workdays' => [1, 2, 3, 4, 5],
                    'is_active' => true,
                    'accepts_signups' => true,
                ],
            );
        }

        return $created;
    }
}
