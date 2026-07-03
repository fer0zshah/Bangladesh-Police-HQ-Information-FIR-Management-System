<?php

namespace Tests\Feature\Admin;

use App\Models\Officer;
use App\Models\Station;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OfficerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_manage_officer_records_without_creating_accounts(): void
    {
        $admin = $this->superAdmin();
        $station = $this->station();

        $this->actingAs($admin)
            ->get(route('admin.officers.index'))
            ->assertOk()
            ->assertSee('Officer registry');

        $this->get(route('admin.officers.create'))
            ->assertOk()
            ->assertSee('This form manages the personnel record only');

        $response = $this->post(route('admin.officers.store'), [
            'station_id' => $station->station_id,
            'name' => 'Inspector Amina Rahman',
            'badge_number' => 'BP-1001',
            'rank' => 'Inspector',
            'status' => 'Active',
        ]);

        $officer = Officer::where('badge_number', 'BP-1001')->firstOrFail();

        $response
            ->assertRedirect(route('admin.officers.show', $officer))
            ->assertSessionHas('success');
        $this->assertNull($officer->user_id);
        $this->assertFalse($officer->is_oc);
        $this->assertDatabaseCount('users', 1);

        $this->get(route('admin.officers.show', $officer))
            ->assertOk()
            ->assertSee('Inspector Amina Rahman')
            ->assertSee('Manual admin action only');

        $this->put(route('admin.officers.update', $officer), [
            'station_id' => $station->station_id,
            'name' => 'Inspector Amina Sultana',
            'badge_number' => 'BP-1001',
            'rank' => 'Senior Inspector',
            'status' => 'Active',
        ])->assertRedirect(route('admin.officers.show', $officer));

        $this->assertDatabaseHas('officers', [
            'officer_id' => $officer->officer_id,
            'name' => 'Inspector Amina Sultana',
            'rank' => 'Senior Inspector',
        ]);

        $this->delete(route('admin.officers.destroy', $officer))
            ->assertRedirect(route('admin.officers.index'));

        $this->assertDatabaseMissing('officers', ['officer_id' => $officer->officer_id]);
    }

    public function test_oc_promotion_requires_explicit_credentials_and_creates_linked_account(): void
    {
        $admin = $this->superAdmin();
        $officer = $this->officer();

        $this->actingAs($admin)
            ->patch(route('admin.officers.toggleOc', $officer))
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertDatabaseCount('users', 1);
        $this->assertFalse($officer->fresh()->is_oc);

        $response = $this->patch(route('admin.officers.toggleOc', $officer), [
            'email' => 'oc.ramna@example.com',
            'phone' => '01711000000',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $officer->refresh();
        $account = $officer->user;

        $response
            ->assertRedirect(route('admin.officers.show', $officer))
            ->assertSessionHas('success');
        $this->assertTrue($officer->is_oc);
        $this->assertNotNull($account);
        $this->assertSame('officer', $account->role);
        $this->assertTrue(Hash::check('SecurePass123!', $account->password));

        $this->actingAs($account)
            ->get(route('oc.dashboard'))
            ->assertOk();

        $this->actingAs($admin)
            ->patch(route('admin.officers.toggleOc', $officer))
            ->assertSessionHas('success');

        $this->assertFalse($officer->fresh()->is_oc);
        $this->assertDatabaseHas('users', [
            'id' => $account->id,
            'role' => 'citizen',
        ]);

        $this->actingAs($account->fresh())
            ->get(route('oc.dashboard'))
            ->assertRedirect('/citizen/my-complaints');
    }

    public function test_repromotion_updates_the_existing_linked_account(): void
    {
        $admin = $this->superAdmin();
        $officer = $this->officer();

        $this->actingAs($admin)->patch(route('admin.officers.toggleOc', $officer), [
            'email' => 'first.oc@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $accountId = $officer->fresh()->user_id;

        $this->patch(route('admin.officers.toggleOc', $officer));
        $this->patch(route('admin.officers.toggleOc', $officer), [
            'email' => 'renamed.oc@example.com',
            'phone' => '01811000000',
            'password' => '',
            'password_confirmation' => '',
        ])->assertSessionHas('success');

        $this->assertSame($accountId, $officer->fresh()->user_id);
        $this->assertDatabaseHas('users', [
            'id' => $accountId,
            'email' => 'renamed.oc@example.com',
            'role' => 'officer',
        ]);
        $this->assertDatabaseCount('users', 2);
    }

    public function test_only_one_oc_can_be_assigned_per_station(): void
    {
        $admin = $this->superAdmin();
        $station = $this->station();
        $first = $this->officer($station, 'BP-2001');
        $second = $this->officer($station, 'BP-2002');

        $this->actingAs($admin)->patch(route('admin.officers.toggleOc', $first), [
            'email' => 'first@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $this->patch(route('admin.officers.toggleOc', $second), [
            'email' => 'second@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ])->assertSessionHasErrors('station_id');

        $this->assertTrue($first->fresh()->is_oc);
        $this->assertFalse($second->fresh()->is_oc);
        $this->assertDatabaseMissing('users', ['email' => 'second@example.com']);
    }

    public function test_inactive_or_unassigned_officer_cannot_become_oc(): void
    {
        $admin = $this->superAdmin();
        $officer = Officer::create([
            'name' => 'Unassigned Officer',
            'badge_number' => 'BP-3001',
            'rank' => 'Inspector',
            'status' => 'Inactive',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.officers.toggleOc', $officer), [
                'email' => 'blocked@example.com',
                'password' => 'SecurePass123!',
                'password_confirmation' => 'SecurePass123!',
            ])
            ->assertSessionHasErrors('station_id');

        $this->assertFalse($officer->fresh()->is_oc);
        $this->assertDatabaseMissing('users', ['email' => 'blocked@example.com']);
    }

    public function test_non_admin_cannot_access_officer_management(): void
    {
        $citizen = User::factory()->create(['role' => 'citizen']);

        $this->actingAs($citizen)
            ->get(route('admin.officers.index'))
            ->assertRedirect('/citizen/my-complaints');
    }

    private function superAdmin(): User
    {
        return User::factory()->create(['role' => 'super_admin']);
    }

    private function station(): Station
    {
        return Station::create([
            'name' => 'Ramna Police Station',
            'district' => 'Dhaka',
            'status' => 'Active',
        ]);
    }

    private function officer(?Station $station = null, string $badge = 'BP-9001'): Officer
    {
        $station ??= $this->station();

        return Officer::create([
            'station_id' => $station->station_id,
            'name' => 'Inspector Karim Hasan',
            'badge_number' => $badge,
            'rank' => 'Inspector',
            'status' => 'Active',
        ]);
    }
}
