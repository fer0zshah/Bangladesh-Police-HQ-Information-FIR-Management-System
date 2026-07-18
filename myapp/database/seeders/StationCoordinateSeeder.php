<?php

namespace Database\Seeders;

use App\Models\Station;
use Illuminate\Database\Seeder;

class StationCoordinateSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * Initial Mapbox demonstration dataset.
         *
         * Coordinates were checked against public map listings based on
         * OpenStreetMap and official police-station address information.
         * Additional stations can be mapped later without changing the UI.
         */
        $coordinates = [
            // Dhaka Metropolitan Police
            'Ramna Model Thana' => [23.7453800, 90.4044000],
            'Shahbag Thana' => [23.7372034, 90.3961773],
            'Dhanmondi Thana' => [23.7433700, 90.3815100],
            'Gulshan Thana' => [23.7914300, 90.4153300],
            'Mirpur Model Thana' => [23.8043941, 90.3630292],

            // Khulna Metropolitan Police
            'Khulna Sadar Thana' => [22.8171000, 89.5649000],
            'Sonadanga Thana' => [22.8160100, 89.5437500],
            'Khalishpur Thana' => [22.8492200, 89.5411600],
            'Daulatpur Thana' => [22.8782900, 89.5184200],
            'Khan Jahan Ali Thana' => [22.9142400, 89.5067800],
            'Aranghata Thana' => [22.8722500, 89.5010400],
        ];

        foreach ($coordinates as $stationName => [$latitude, $longitude]) {
            $updated = Station::query()
                ->where('name', $stationName)
                ->where('type', 'thana')
                ->update(compact('latitude', 'longitude'));

            if ($updated === 0) {
                $this->command?->warn("Station not found; coordinate skipped: {$stationName}");
            }
        }
    }
}
