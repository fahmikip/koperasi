<?php

namespace Tests\Feature;

use App\Models\Installment;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Saving;
use App\Models\SavingType;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::where('email', 'admin@koperasi.test')->firstOrFail();
        $member = Member::factory()->create(['name' => 'Budi Laporan']);
        $type = SavingType::firstOrFail();
        Saving::create(['transaction_number' => 'SAV-REPORT-001', 'member_id' => $member->id, 'saving_type_id' => $type->id, 'created_by' => $this->admin->id, 'transaction_date' => now(), 'direction' => 'deposit', 'amount' => 500000]);
        $loan = Loan::create(['loan_number' => 'LOAN-REPORT-001', 'member_id' => $member->id, 'approved_by' => $this->admin->id, 'principal_amount' => 1000000, 'interest_rate' => 1, 'term_months' => 10, 'total_interest' => 100000, 'total_payable' => 1100000, 'remaining_balance' => 900000, 'status' => 'disbursed', 'applied_at' => now(), 'approved_at' => now(), 'disbursed_at' => now(), 'purpose' => 'Pengujian laporan']);
        Installment::create(['payment_number' => 'PAY-REPORT-001', 'loan_id' => $loan->id, 'received_by' => $this->admin->id, 'installment_number' => 1, 'paid_at' => now(), 'principal_paid' => 100000, 'interest_paid' => 10000, 'penalty' => 5000, 'total_paid' => 115000]);
    }

    public function test_authorized_user_can_view_all_report_types(): void
    {
        foreach (['members', 'savings', 'loans', 'installments', 'transactions'] as $type) {
            $this->actingAs($this->admin)->get(route('reports.index', $type))->assertOk()->assertSee('Pusat Laporan');
        }
    }

    public function test_report_filters_validate_date_range(): void
    {
        $this->actingAs($this->admin)->get(route('reports.index', ['type' => 'savings', 'date_from' => '2026-07-18', 'date_to' => '2026-07-17']))->assertSessionHasErrors('date_to');
    }

    public function test_reports_can_be_exported_to_pdf_and_excel(): void
    {
        $this->actingAs($this->admin)->get(route('reports.pdf', 'transactions'))->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->actingAs($this->admin)->get(route('reports.excel', 'members'))->assertOk()->assertDownload();
    }

    public function test_user_without_report_permission_is_forbidden(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('reports.index'))->assertForbidden();
    }
}
