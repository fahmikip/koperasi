<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_create_member_with_unique_number(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::where('email', 'admin@koperasi.test')->firstOrFail();
        $data = ['nik' => '3273010101010001', 'name' => 'Budi Santoso', 'birth_place' => 'Bandung', 'birth_date' => '1990-01-01', 'gender' => 'male', 'address' => 'Jalan Merdeka', 'district' => 'Coblong', 'regency' => 'Bandung', 'province' => 'Jawa Barat', 'whatsapp' => '081234567890', 'email' => 'budi@example.test', 'occupation' => 'Wiraswasta', 'joined_at' => '2026-01-01', 'valid_until' => '2031-01-01', 'status' => 'active'];
        $this->actingAs($user)->post(route('members.store'), $data)->assertRedirect();
        $this->assertDatabaseHas('members', ['member_number' => 'KOP-'.now()->year.'-000001', 'nik' => $data['nik']]);
    }

    public function test_guest_cannot_access_member_management(): void
    {
        $this->get(route('members.index'))->assertRedirect(route('login'));
    }
}
