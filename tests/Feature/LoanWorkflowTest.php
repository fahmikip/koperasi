<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Member $member;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::where('email', 'admin@koperasi.test')->firstOrFail();
        $this->member = Member::factory()->create();
    }

    public function test_authorized_user_can_submit_approve_and_disburse_a_loan(): void
    {
        $this->actingAs($this->admin)->post(route('loans.store'), $this->payload())->assertRedirect();
        $loan = Loan::firstOrFail();

        $this->assertSame(Loan::STATUS_SUBMITTED, $loan->status);
        $this->assertEquals('120000.00', $loan->total_interest);
        $this->assertEquals('1120000.00', $loan->total_payable);
        $this->assertEquals('0.00', $loan->remaining_balance);

        $this->actingAs($this->admin)->post(route('loans.approve', $loan), ['review_notes' => 'Layak'])->assertRedirect();
        $loan->refresh();
        $this->assertSame(Loan::STATUS_APPROVED, $loan->status);
        $this->assertSame($this->admin->id, $loan->approved_by);

        $this->actingAs($this->admin)->post(route('loans.disburse', $loan), ['disbursed_at' => now()->format('Y-m-d'), 'disbursement_notes' => 'Transfer bank'])->assertRedirect();
        $loan->refresh();
        $this->assertSame(Loan::STATUS_DISBURSED, $loan->status);
        $this->assertEquals($loan->principal_amount, $loan->remaining_balance);
    }

    public function test_rejection_requires_a_reason(): void
    {
        $loan = $this->submitLoan();

        $this->actingAs($this->admin)->post(route('loans.reject', $loan), [])->assertSessionHasErrors('review_notes');
        $this->assertSame(Loan::STATUS_SUBMITTED, $loan->fresh()->status);
    }

    public function test_submitted_loan_cannot_be_disbursed(): void
    {
        $loan = $this->submitLoan();

        $this->actingAs($this->admin)->post(route('loans.disburse', $loan), ['disbursed_at' => now()->format('Y-m-d')])->assertForbidden();
        $this->assertSame(Loan::STATUS_SUBMITTED, $loan->fresh()->status);
    }

    public function test_petugas_can_submit_but_cannot_approve(): void
    {
        $petugas = User::factory()->create();
        $petugas->assignRole('Petugas');

        $this->actingAs($petugas)->post(route('loans.store'), $this->payload())->assertRedirect();
        $loan = Loan::firstOrFail();
        $this->actingAs($petugas)->post(route('loans.approve', $loan))->assertForbidden();
    }

    public function test_approved_loan_cannot_be_edited_or_deleted(): void
    {
        $loan = $this->submitLoan();
        $this->actingAs($this->admin)->post(route('loans.approve', $loan));
        $loan->refresh();

        $this->actingAs($this->admin)->put(route('loans.update', $loan), $this->payload())->assertForbidden();
        $this->actingAs($this->admin)->delete(route('loans.destroy', $loan))->assertForbidden();
    }

    private function submitLoan(): Loan
    {
        $this->actingAs($this->admin)->post(route('loans.store'), $this->payload());

        return Loan::firstOrFail();
    }

    private function payload(): array
    {
        return [
            'member_id' => $this->member->id,
            'principal_amount' => 1000000,
            'interest_rate' => 1,
            'term_months' => 12,
            'applied_at' => now()->format('Y-m-d'),
            'purpose' => 'Modal usaha',
            'notes' => 'Pengajuan pengujian',
        ];
    }
}
