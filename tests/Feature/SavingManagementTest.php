<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Saving;
use App\Models\SavingType;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavingManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Member $member;

    private SavingType $type;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->user = User::where('email', 'admin@koperasi.test')->firstOrFail();
        $this->member = Member::factory()->create();
        $this->type = SavingType::where('name', 'Simpanan Sukarela')->firstOrFail();
    }

    public function test_authorized_user_can_record_deposit_and_withdrawal(): void
    {
        $this->actingAs($this->user)->post(route('savings.store'), $this->payload('deposit', 150000))->assertRedirect();
        $this->actingAs($this->user)->post(route('savings.store'), $this->payload('withdrawal', 50000))->assertRedirect();

        $this->assertDatabaseCount('savings', 2);
        $this->assertEquals(100000, Saving::query()->selectRaw("SUM(CASE WHEN direction = 'deposit' THEN amount ELSE -amount END) as balance")->value('balance'));
        $this->assertNotSame(Saving::first()->transaction_number, Saving::latest('id')->first()->transaction_number);
    }

    public function test_withdrawal_cannot_exceed_available_balance(): void
    {
        $response = $this->actingAs($this->user)->from(route('savings.create'))->post(route('savings.store'), $this->payload('withdrawal', 1));

        $response->assertRedirect(route('savings.create'))->assertSessionHasErrors('amount');
        $this->assertDatabaseCount('savings', 0);
    }

    public function test_deposit_cannot_be_reduced_below_a_later_withdrawal(): void
    {
        $this->actingAs($this->user)->post(route('savings.store'), $this->payload('deposit', 100000, '2026-07-01'));
        $this->actingAs($this->user)->post(route('savings.store'), $this->payload('withdrawal', 80000, '2026-07-02'));
        $deposit = Saving::where('direction', 'deposit')->firstOrFail();

        $this->actingAs($this->user)->put(route('savings.update', $deposit), $this->payload('deposit', 50000, '2026-07-01'))
            ->assertSessionHasErrors('amount');

        $this->assertEquals('100000.00', $deposit->fresh()->amount);
    }

    public function test_deposit_cannot_be_deleted_when_it_supports_a_later_withdrawal(): void
    {
        $this->actingAs($this->user)->post(route('savings.store'), $this->payload('deposit', 100000, '2026-07-01'));
        $this->actingAs($this->user)->post(route('savings.store'), $this->payload('withdrawal', 80000, '2026-07-02'));
        $deposit = Saving::where('direction', 'deposit')->firstOrFail();

        $this->actingAs($this->user)->delete(route('savings.destroy', $deposit))->assertSessionHasErrors('amount');

        $this->assertDatabaseHas('savings', ['id' => $deposit->id]);
    }

    public function test_guest_cannot_access_savings(): void
    {
        $this->get(route('savings.index'))->assertRedirect(route('login'));
    }

    private function payload(string $direction, int $amount, ?string $date = null): array
    {
        return [
            'member_id' => $this->member->id,
            'saving_type_id' => $this->type->id,
            'transaction_date' => $date ?? now()->format('Y-m-d'),
            'direction' => $direction,
            'amount' => $amount,
            'notes' => 'Transaksi pengujian',
        ];
    }
}
