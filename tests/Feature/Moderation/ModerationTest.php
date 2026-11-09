<?php

namespace Tests\Feature\Moderation;

use App\Models\Event;
use App\Models\Organization;
use App\Models\QuotaLedger;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ModerationTest extends TestCase
{
    use RefreshDatabase;

    protected function createOrganizationWithMember(string $role = 'owner'): array
    {
        $user = User::factory()->create([
            'role' => User::ROLE_PESERTA,
        ]);

        $organization = Organization::create([
            'name' => 'Cycling Club Bandung',
            'slug' => 'cycling-club-bandung',
            'description' => 'Komunitas balap sepeda amatir',
            'verification_status' => Organization::VERIFICATION_UNVERIFIED,
        ]);

        $organization->users()->attach($user->id, [
            'role' => $role,
        ]);

        return [$organization, $user];
    }

    public function test_organization_member_can_upload_verification_document(): void
    {
        Storage::fake('local');
        [$organization, $user] = $this->createOrganizationWithMember();

        $file = UploadedFile::fake()->create('ktp_penanggung_jawab.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->post(route('organizations.verification.submit', ['organization' => $organization->slug]), [
            'document' => $file,
        ]);

        $response->assertRedirect(route('organizations.verification', ['organization' => $organization->slug]));
        $organization->refresh();

        $this->assertSame(Organization::VERIFICATION_PENDING, $organization->verification_status);
        $this->assertNotNull($organization->verification_document_path);
        Storage::disk('local')->assertExists($organization->verification_document_path);
    }

    public function test_admin_can_approve_organization_verification(): void
    {
        [$organization] = $this->createOrganizationWithMember();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $organization->approveVerification($admin);
        $organization->refresh();

        $this->assertTrue($organization->isVerified());
        $this->assertSame(Organization::VERIFICATION_VERIFIED, $organization->verification_status);
        $this->assertNotNull($organization->verified_at);
        $this->assertSame($admin->id, $organization->verified_by);
        $this->assertNull($organization->rejection_note);
    }

    public function test_admin_can_reject_organization_verification_with_reason(): void
    {
        [$organization] = $this->createOrganizationWithMember();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $organization->rejectVerification($admin, 'Foto KTP tidak terbaca dengan jelas.');
        $organization->refresh();

        $this->assertFalse($organization->isVerified());
        $this->assertTrue($organization->isRejectedVerification());
        $this->assertSame('Foto KTP tidak terbaca dengan jelas.', $organization->rejection_note);
        $this->assertNull($organization->verified_at);
    }

    public function test_unverified_organization_cannot_submit_or_publish_event(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Komunitas / organisasi belum diverifikasi oleh admin Velosha');

        Event::factory()->create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'status' => Event::STATUS_SUBMITTED,
        ]);
    }

    public function test_verified_organization_can_submit_event_for_moderation_without_spending_quota(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $organization->approveVerification($admin);

        QuotaLedger::create([
            'organization_id' => $organization->id,
            'delta' => 1,
            'reason' => 'purchase',
            'created_at' => now(),
        ]);

        $event = Event::factory()->create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'status' => Event::STATUS_DRAFT,
        ]);

        $this->assertSame(1, $organization->quotaBalance());

        $event->submitForReview();
        $event->refresh();

        $this->assertSame(Event::STATUS_SUBMITTED, $event->status);
        $this->assertTrue($event->isSubmitted());
        // Kuota belum dipotong selama berstatus submitted
        $this->assertSame(1, $organization->quotaBalance());
    }

    public function test_admin_approving_submitted_event_publishes_it_and_deducts_quota(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $organization->approveVerification($admin);

        QuotaLedger::create([
            'organization_id' => $organization->id,
            'delta' => 1,
            'reason' => 'purchase',
            'created_at' => now(),
        ]);

        $event = Event::factory()->create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'status' => Event::STATUS_DRAFT,
        ]);

        $event->submitForReview();
        $this->assertSame(1, $organization->quotaBalance());

        // Admin setujui
        $event->approve($admin);
        $event->refresh();

        $this->assertSame(Event::STATUS_PUBLISHED, $event->status);
        $this->assertSame($admin->id, $event->reviewed_by);
        $this->assertNotNull($event->reviewed_at);
        // Kuota terpotong 1
        $this->assertSame(0, $organization->quotaBalance());
    }

    public function test_admin_rejecting_submitted_event_keeps_quota_intact(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $organization->approveVerification($admin);

        QuotaLedger::create([
            'organization_id' => $organization->id,
            'delta' => 1,
            'reason' => 'purchase',
            'created_at' => now(),
        ]);

        $event = Event::factory()->create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'status' => Event::STATUS_SUBMITTED,
        ]);

        $this->assertSame(1, $organization->quotaBalance());

        // Admin tolak event
        $event->reject($admin, 'Elevasi dan gpx tidak sinkron.');
        $event->refresh();

        $this->assertSame(Event::STATUS_REJECTED, $event->status);
        $this->assertTrue($event->isRejected());
        $this->assertSame('Elevasi dan gpx tidak sinkron.', $event->rejection_note);
        // Kuota tetap 1 (tidak berkurang)
        $this->assertSame(1, $organization->quotaBalance());
    }

    public function test_non_member_cannot_download_private_verification_document(): void
    {
        Storage::fake('local');
        [$organization, $member] = $this->createOrganizationWithMember();
        $outsider = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $path = Storage::disk('local')->putFile('verification_documents', UploadedFile::fake()->create('ktp.pdf', 100));
        $organization->update(['verification_document_path' => $path]);

        // Orang luar -> 403
        $this->actingAs($outsider)
            ->get(route('organizations.document.download', ['organization' => $organization->slug]))
            ->assertStatus(403);

        // Anggota sendiri -> 200
        $this->actingAs($member)
            ->get(route('organizations.document.download', ['organization' => $organization->slug]))
            ->assertStatus(200);

        // Admin platform -> 200
        $this->actingAs($admin)
            ->get(route('organizations.document.download', ['organization' => $organization->slug]))
            ->assertStatus(200);
    }

    public function test_public_catalog_and_show_only_display_published_events(): void
    {
        $published = Event::factory()->create([
            'title' => 'Tour de Bandung (Published)',
            'status' => Event::STATUS_PUBLISHED,
        ]);

        $draft = Event::factory()->create([
            'title' => 'Secret Race (Draft)',
            'status' => Event::STATUS_DRAFT,
        ]);

        $submitted = Event::factory()->create([
            'title' => 'Pending Race (Submitted)',
            'status' => Event::STATUS_SUBMITTED,
        ]);

        $rejected = Event::factory()->create([
            'title' => 'Rejected Race (Rejected)',
            'status' => Event::STATUS_REJECTED,
        ]);

        // Katalog /events
        $catalogResponse = $this->get(route('events.index'));
        $catalogResponse->assertStatus(200);
        $catalogResponse->assertSee($published->title);
        $catalogResponse->assertDontSee($draft->title);
        $catalogResponse->assertDontSee($submitted->title);
        $catalogResponse->assertDontSee($rejected->title);

        // Detail published -> 200
        $this->get(route('events.show', ['slug' => $published->slug]))
            ->assertStatus(200);

        // Detail draft / submitted / rejected -> 404
        $this->get(route('events.show', ['slug' => $draft->slug]))
            ->assertStatus(404);
        $this->get(route('events.show', ['slug' => $submitted->slug]))
            ->assertStatus(404);
        $this->get(route('events.show', ['slug' => $rejected->slug]))
            ->assertStatus(404);
    }

    public function test_non_admin_organizer_cannot_create_event_without_organization(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_PENYELENGGARA]);

        $this->actingAs($user);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Penyelenggara wajib memilih komunitas / organisasi untuk event ini.');

        Event::factory()->create([
            'user_id' => $user->id,
            'organization_id' => null,
            'status' => Event::STATUS_DRAFT,
        ]);
    }
}

