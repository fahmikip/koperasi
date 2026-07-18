<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_preview_and_download_modern_member_card(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::where('email', 'admin@koperasi.test')->firstOrFail();
        $member = Member::factory()->create(['name' => 'Siti Rahmawati']);

        $this->actingAs($user)->get(route('members.card', $member))
            ->assertOk()
            ->assertSee('KOPERASI MODERN')
            ->assertSee('Siti Rahmawati');

        $this->actingAs($user)->get(route('members.card.download', $member))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->assertDatabaseHas('card_print_histories', ['member_id' => $member->id, 'action' => 'download']);
    }

    public function test_public_qr_verification_page_shows_member_status(): void
    {
        $member = Member::factory()->create(['status' => 'active']);

        $this->get(route('members.verify', $member->qr_token))->assertOk()->assertSee('Anggota Aktif')->assertSee($member->member_number);
    }

    public function test_missing_photo_file_uses_initial_fallback_instead_of_broken_image(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::where('email', 'admin@koperasi.test')->firstOrFail();
        $member = Member::factory()->create(['name' => 'Ahmad', 'photo_path' => 'members/missing-photo.jpg']);

        $this->assertFalse($member->hasStoredPhoto());
        $this->assertNull($member->photoUrl());
        $this->actingAs($user)->get(route('members.show', $member))->assertOk()->assertDontSee('missing-photo.jpg');
    }
}
