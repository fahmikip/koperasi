<?php

namespace Tests\Unit;

use App\Models\Saving;
use PHPUnit\Framework\TestCase;

class SavingTest extends TestCase
{
    public function test_deposit_has_positive_signed_amount(): void
    {
        $saving = new Saving(['direction' => 'deposit', 'amount' => '125000.50']);

        $this->assertSame(12500050, $saving->signedAmountInCents());
    }

    public function test_withdrawal_has_negative_signed_amount(): void
    {
        $saving = new Saving(['direction' => 'withdrawal', 'amount' => '50000.25']);

        $this->assertSame(-5000025, $saving->signedAmountInCents());
    }
}
