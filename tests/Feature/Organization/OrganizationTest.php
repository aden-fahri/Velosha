<?php

namespace Tests\Feature\Organization;

use App\Filament\Resources\Events\EventResource;
use App\Models\Event;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_organization_registration(): void
    {
        $response = $this->get(route('organizations.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_register_organization_and_becomes_owner(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_PESERTA,
        ]);

        $response = $this->actingAs($user)->post(route('organizations.store'), [
            'name' => 'Bromo Cycling Club',
            'description' => 'Komunitas gowes tanjakan Bromo',
        ]);

        $response->assertRedirect('/admin/events/create');
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('organizations', [
            'name' => 'Bromo Cycling Club',
            'slug' => 'bromo-cycling-club',
            'description' => 'Komunitas gowes tanjakan Bromo',
        ]);

        $org = Organization::where('slug', 'bromo-cycling-club')->first();
        $this->assertNotNull($org);

        $this->assertDatabaseHas('organization_members', [
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'role' => 'owner',
        ]);

        $this->assertTrue($user->organizations->contains($org));
        $this->assertTrue($user->ownedOrganizations->contains($org));
        $this->assertTrue($user->canAccessPanel(new \Filament\Panel()));
    }

    public function test_event_can_be_associated_with_organization(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_PENYELENGGARA]);
        $org = Organization::create([
            'name' => 'Nusantara Gran Fondo',
            'slug' => 'nusantara-gran-fondo',
        ]);
        $org->users()->attach($user->id, ['role' => 'owner']);

        $event = Event::factory()->create([
            'user_id' => $user->id,
            'organization_id' => $org->id,
            'title' => 'Bromo KOM Challenge',
        ]);

        $this->assertNotNull($event->organization);
        $this->assertSame($org->id, $event->organization->id);
        $this->assertTrue($org->events->contains($event));
    }

    public function test_organizer_query_scoped_to_their_organization_events(): void
    {
        $userA = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a']);
        $orgA->users()->attach($userA->id, ['role' => 'owner']);

        $userB = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b']);
        $orgB->users()->attach($userB->id, ['role' => 'owner']);

        $eventA = Event::factory()->create([
            'user_id' => $userA->id,
            'organization_id' => $orgA->id,
            'title' => 'Event A',
        ]);

        $eventB = Event::factory()->create([
            'user_id' => $userB->id,
            'organization_id' => $orgB->id,
            'title' => 'Event B',
        ]);

        // Simulasikan login userA
        $this->actingAs($userA);
        $queryA = EventResource::getEloquentQuery();
        $resultsA = $queryA->pluck('id');

        $this->assertTrue($resultsA->contains($eventA->id));
        $this->assertFalse($resultsA->contains($eventB->id));

        // Simulasikan admin
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin);
        $queryAdmin = EventResource::getEloquentQuery();
        $resultsAdmin = $queryAdmin->pluck('id');

        $this->assertTrue($resultsAdmin->contains($eventA->id));
        $this->assertTrue($resultsAdmin->contains($eventB->id));
    }
}

