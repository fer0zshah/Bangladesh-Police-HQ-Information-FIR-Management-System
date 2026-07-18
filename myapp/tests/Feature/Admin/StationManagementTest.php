<?php

namespace Tests\Feature\Admin;

use App\Models\CaseFir;
use App\Models\CitizenComplaint;
use App\Models\Officer;
use App\Models\Station;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_station_workload(): void
    {
        $station = Station::create([
            'name' => 'Dhanmondi Police Station',
            'district' => 'Dhaka',
            'status' => 'Active',
        ]);
        $officer = Officer::create([
            'station_id' => $station->station_id,
            'name' => 'Amina Rahman',
            'badge_number' => 'DMP-1001',
            'rank' => 'Inspector',
            'status' => 'Active',
        ]);
        $complaint = CitizenComplaint::create([
            'station_id' => $station->station_id,
            'complainant_name' => 'Citizen One',
            'complainant_nid' => '1234567890',
            'complaint_title' => 'A test complaint',
            'description' => 'A test complaint.',
            'submitted_date' => now()->toDateString(),
            'status' => 'Pending',
        ]);
        CaseFir::create([
            'station_id' => $station->station_id,
            'investigating_officer_id' => $officer->officer_id,
            'complaint_id' => $complaint->complaint_id,
            'case_title' => 'Test Case File',
            'date_filed' => now()->toDateString(),
            'status' => 'Under Investigation',
        ]);

        $indexResponse = $this->actingAs($this->superAdmin())
            ->get(route('admin.stations.index'));

        $indexResponse
            ->assertOk()
            ->assertSee('Dhanmondi Police Station')
            ->assertViewHas('stations', fn ($stations) => $stations->first()->officers_count === 1
                && $stations->first()->cases_count === 1
            );

        $showResponse = $this->get(route('admin.stations.show', $station));

        $showResponse
            ->assertOk()
            ->assertSee('Amina Rahman')
            ->assertSee('Test Case File')
            ->assertViewHas('caseStats', fn ($stats) => $stats->total_cases === 1
                && $stats->active_cases === 1
                && $stats->closed_cases === 0
            );
    }

    public function test_super_admin_can_create_and_update_a_station(): void
    {
        $admin = $this->superAdmin();

        $this->actingAs($admin)
            ->get(route('admin.stations.create'))
            ->assertOk()
            ->assertSee('Station information');

        $createResponse = $this->actingAs($admin)->post(route('admin.stations.store'), [
            'name' => 'Uttara East Police Station',
            'district' => 'Dhaka',
            'address' => 'Uttara, Dhaka',
            'contact_number' => '+8801711000000',
        ]);

        $station = Station::where('name', 'Uttara East Police Station')->firstOrFail();

        $createResponse
            ->assertRedirect(route('admin.stations.index'))
            ->assertSessionHas('success');
        $this->assertSame('Active', $station->status);

        $this->get(route('admin.stations.edit', $station))
            ->assertOk()
            ->assertSee('Editing registry #'.$station->station_id);

        $updateResponse = $this->put(route('admin.stations.update', $station), [
            'name' => 'Uttara Model Police Station',
            'district' => 'Dhaka',
            'address' => 'Sector 4, Uttara, Dhaka',
            'contact_number' => '+8801711000001',
            'status' => 'Inactive',
        ]);

        $updateResponse
            ->assertRedirect(route('admin.stations.index'))
            ->assertSessionHas('success');
        $this->assertDatabaseHas('stations', [
            'station_id' => $station->station_id,
            'name' => 'Uttara Model Police Station',
            'status' => 'Inactive',
        ]);
    }

    public function test_super_admin_can_toggle_station_status_without_deleting_it(): void
    {
        $station = Station::create([
            'name' => 'Mirpur Police Station',
            'district' => 'Dhaka',
            'status' => 'Active',
        ]);

        $response = $this->actingAs($this->superAdmin())
            ->patch(route('admin.stations.toggle-status', $station));

        $response
            ->assertRedirect(route('admin.stations.index'))
            ->assertSessionHas('success', 'Station deactivated successfully.');
        $this->assertDatabaseHas('stations', [
            'station_id' => $station->station_id,
            'status' => 'Inactive',
        ]);

        $this->patch(route('admin.stations.toggle-status', $station))
            ->assertSessionHas('success', 'Station activated successfully.');
        $this->assertDatabaseHas('stations', [
            'station_id' => $station->station_id,
            'status' => 'Active',
        ]);
    }

    public function test_station_fields_are_validated(): void
    {
        $response = $this->actingAs($this->superAdmin())
            ->from(route('admin.stations.create'))
            ->post(route('admin.stations.store'), [
                'name' => '',
                'district' => '',
                'contact_number' => str_repeat('1', 16),
            ]);

        $response
            ->assertRedirect(route('admin.stations.create'))
            ->assertSessionHasErrors(['name', 'district', 'contact_number']);
        $this->assertDatabaseMissing('stations', ['name' => '']);
    }

    public function test_non_admin_cannot_access_station_management(): void
    {
        $citizen = User::factory()->create(['role' => 'citizen']);

        $this->actingAs($citizen)
            ->get(route('admin.stations.index'))
            ->assertRedirect('/');
    }

    private function superAdmin(): User
    {
        return User::factory()->create(['role' => 'super_admin']);
    }
}
