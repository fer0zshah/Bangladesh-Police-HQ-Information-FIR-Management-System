<?php

namespace Tests\Feature;

use App\Models\Criminal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicWantedCriminalTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_browse_only_wanted_criminals(): void
    {
        $wanted = Criminal::create([
            'nid_number' => 'WANTED-1001',
            'name' => 'Wanted Person',
            'alias' => 'Red Fox',
            'wanted_status' => true,
        ]);

        Criminal::create([
            'nid_number' => 'CLEAR-1001',
            'name' => 'Cleared Person',
            'wanted_status' => false,
        ]);

        $this->get(route('wanted-criminals.index'))
            ->assertOk()
            ->assertSee($wanted->name)
            ->assertSee($wanted->alias)
            ->assertDontSee('Cleared Person')
            ->assertDontSee($wanted->nid_number);
    }

    public function test_profile_requires_login_and_hides_sensitive_fields(): void
    {
        $wanted = Criminal::create([
            'nid_number' => 'WANTED-2001',
            'name' => 'Profile Person',
            'alias' => 'Night Bird',
            'date_of_birth' => '1990-01-02',
            'wanted_status' => true,
        ]);

        $this->get(route('wanted-criminals.show', $wanted))
            ->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create(['role' => 'citizen']))
            ->get(route('wanted-criminals.show', $wanted))
            ->assertOk()
            ->assertSee($wanted->name)
            ->assertSee($wanted->alias)
            ->assertDontSee($wanted->nid_number)
            ->assertDontSee('Recorded case links');
    }

    public function test_public_fir_routes_are_removed(): void
    {
        $this->get('/public-cases')->assertNotFound();
        $this->get('/stations/1/cases/1')->assertNotFound();
    }
}
