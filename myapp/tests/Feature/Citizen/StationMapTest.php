<?php

namespace Tests\Feature\Citizen;

use App\Models\Station;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StationMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_read_the_citizen_station_map_feed(): void
    {
        $this->getJson(route('citizen.station-options.map'))
            ->assertUnauthorized();
    }

    public function test_map_feed_contains_only_active_thanas_with_coordinates(): void
    {
        $citizen = User::factory()->create(['role' => 'citizen']);

        $command = Station::create([
            'name' => 'Khulna Metropolitan Police',
            'type' => 'metropolitanHQ',
            'district' => 'Khulna',
            'division' => 'Khulna',
            'head_rank' => 'Police Commissioner',
            'status' => 'Active',
            'is_active' => true,
        ]);

        $mapped = Station::create([
            'name' => 'Khan Jahan Ali Thana',
            'type' => 'thana',
            'parent_id' => $command->station_id,
            'district' => 'Khulna',
            'division' => 'Khulna',
            'head_rank' => 'OC',
            'status' => 'Active',
            'is_active' => true,
            'latitude' => 22.9142400,
            'longitude' => 89.5067800,
        ]);

        Station::create([
            'name' => 'Unmapped Thana',
            'type' => 'thana',
            'district' => 'Khulna',
            'division' => 'Khulna',
            'head_rank' => 'OC',
            'status' => 'Active',
            'is_active' => true,
        ]);

        Station::create([
            'name' => 'Inactive Mapped Thana',
            'type' => 'thana',
            'district' => 'Khulna',
            'division' => 'Khulna',
            'head_rank' => 'OC',
            'status' => 'Inactive',
            'is_active' => false,
            'latitude' => 22.8000000,
            'longitude' => 89.5000000,
        ]);

        $this->actingAs($citizen)
            ->getJson(route('citizen.station-options.map'))
            ->assertOk()
            ->assertJsonCount(1, 'stations')
            ->assertJsonPath('stations.0.station_id', $mapped->station_id)
            ->assertJsonPath('stations.0.command_name', 'Khulna Metropolitan Police')
            ->assertJsonPath('stations.0.latitude', 22.91424)
            ->assertJsonMissing(['name' => 'Unmapped Thana'])
            ->assertJsonMissing(['name' => 'Inactive Mapped Thana']);
    }

    public function test_complaint_form_contains_the_nearest_station_map_interface(): void
    {
        config(['services.mapbox.public_token' => 'pk.test-token']);

        $citizen = User::factory()->create(['role' => 'citizen']);

        $this->actingAs($citizen)
            ->get(route('citizen.complaints.create'))
            ->assertOk()
            ->assertSee('Find Nearest Station')
            ->assertSee('pk.test-token');
    }
}
