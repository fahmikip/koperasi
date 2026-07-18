<?php

namespace Tests\Unit;

use App\Models\Loan;
use App\Repositories\Contracts\LoanRepositoryInterface;
use App\Services\LoanService;
use PHPUnit\Framework\TestCase;

class LoanTest extends TestCase
{
    public function test_flat_interest_calculation(): void
    {
        $repository = $this->createMock(LoanRepositoryInterface::class);
        $service = new LoanService($repository);

        $this->assertSame(['total_interest' => 120000.0, 'total_payable' => 1120000.0], $service->calculate(1000000, 1, 12));
    }

    public function test_state_transition_helpers(): void
    {
        $loan = new Loan(['status' => Loan::STATUS_SUBMITTED]);
        $this->assertTrue($loan->canBeReviewed());
        $this->assertTrue($loan->canBeModified());
        $this->assertFalse($loan->canBeDisbursed());

        $loan->status = Loan::STATUS_APPROVED;
        $this->assertFalse($loan->canBeReviewed());
        $this->assertFalse($loan->canBeModified());
        $this->assertTrue($loan->canBeDisbursed());
    }
}
