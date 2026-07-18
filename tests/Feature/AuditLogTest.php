<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::where('email', 'admin@koperasi.test')->firstOrFail();
    }

    public function test_authorized_user_can_search_and_filter_audit_logs(): void
    {
        activity()->causedBy($this->admin)->withProperties(['ip' => '127.0.0.1'])->log('login');
        $member = Member::factory()->create(['name' => 'Nama Lama']);
        $member->update(['name' => 'Nama Baru']);

        $this->actingAs($this->admin)->get(route('audit.index', ['event' => 'updated']))
            ->assertOk()
            ->assertSee('Penelusuran Audit Log')
            ->assertSee('Diperbarui')
            ->assertSee($member->member_number);
        $this->actingAs($this->admin)->get(route('audit.index', ['event' => 'login']))->assertOk()->assertSee('Masuk Sistem');
    }

    public function test_audit_detail_shows_old_and_new_values(): void
    {
        $member = Member::factory()->create(['name' => 'Nama Lama']);
        $member->update(['name' => 'Nama Baru']);
        $activity = Activity::where('event', 'updated')->latest('id')->firstOrFail();

        $this->actingAs($this->admin)->get(route('audit.show', $activity))
            ->assertOk()
            ->assertSee('Detail Aktivitas')
            ->assertSee('Nama Lama')
            ->assertSee('Nama Baru');
    }

    public function test_user_without_permission_cannot_access_audit_logs(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('audit.index'))->assertForbidden();
        $activity = activity()->log('system test');
        $this->actingAs($user)->get(route('audit.show', $activity))->assertForbidden();
    }
}
