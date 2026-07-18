<?php

namespace Tests\Feature;

use App\Models\Installment;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstallmentPaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Loan $loan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::where('email', 'admin@koperasi.test')->firstOrFail();
        $member = Member::factory()->create();
        $this->actingAs($this->admin)->post(route('loans.store'), [
            'member_id' => $member->id,
            'principal_amount' => 1000000,
            'interest_rate' => 1,
            'term_months' => 2,
            'applied_at' => now()->format('Y-m-d'),
            'purpose' => 'Modal usaha',
        ]);
        $this->loan = Loan::firstOrFail();
        $this->actingAs($this->admin)->post(route('loans.approve', $this->loan));
        $this->loan->refresh();
        $this->actingAs($this->admin)->post(route('loans.disburse', $this->loan), ['disbursed_at' => now()->format('Y-m-d')]);
        $this->loan->refresh();
    }

    public function test_payment_reduces_principal_and_calculates_total_with_penalty(): void
    {
        $this->actingAs($this->admin)->post(route('installments.store', $this->loan), $this->payload(400000, 10000, 25000))->assertRedirect();

        $installment = Installment::firstOrFail();
        $this->assertEquals('435000.00', $installment->total_paid);
        $this->assertEquals(1, $installment->installment_number);
        $this->assertEquals('600000.00', $this->loan->fresh()->remaining_balance);
        $this->assertSame(Loan::STATUS_DISBURSED, $this->loan->fresh()->status);
    }

    public function test_payment_cannot_exceed_remaining_principal_or_interest(): void
    {
        $this->actingAs($this->admin)->post(route('installments.store', $this->loan), $this->payload(1000001, 0, 0))->assertSessionHasErrors('principal_paid');
        $this->actingAs($this->admin)->post(route('installments.store', $this->loan), $this->payload(0, 20001, 0))->assertSessionHasErrors('interest_paid');

        $this->assertDatabaseCount('installments', 0);
        $this->assertEquals('1000000.00', $this->loan->fresh()->remaining_balance);
    }

    public function test_full_principal_and_interest_payment_marks_loan_paid(): void
    {
        $this->actingAs($this->admin)->post(route('installments.store', $this->loan), $this->payload(1000000, 20000, 5000))->assertRedirect();

        $loan = $this->loan->fresh();
        $this->assertSame(Loan::STATUS_PAID, $loan->status);
        $this->assertEquals('0.00', $loan->remaining_balance);
        $this->actingAs($this->admin)->get(route('installments.create', $loan))->assertForbidden();
    }

    public function test_payment_date_cannot_precede_disbursement_date(): void
    {
        $this->loan->update(['disbursed_at' => now()->subDay()]);
        $data = $this->payload(100000, 1000, 0);
        $data['paid_at'] = now()->subDays(2)->format('Y-m-d');

        $this->actingAs($this->admin)->post(route('installments.store', $this->loan), $data)->assertSessionHasErrors('paid_at');
        $this->assertDatabaseCount('installments', 0);
    }

    public function test_zero_payment_is_rejected(): void
    {
        $this->actingAs($this->admin)->post(route('installments.store', $this->loan), $this->payload(0, 0, 0))->assertSessionHasErrors('principal_paid');
    }

    private function payload(float $principal, float $interest, float $penalty): array
    {
        return ['paid_at' => now()->format('Y-m-d'), 'principal_paid' => $principal, 'interest_paid' => $interest, 'penalty' => $penalty, 'notes' => 'Pembayaran pengujian'];
    }
}
