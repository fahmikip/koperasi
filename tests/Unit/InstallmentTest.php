<?php

namespace Tests\Unit;

use App\Repositories\Contracts\InstallmentRepositoryInterface;
use App\Repositories\Contracts\LoanRepositoryInterface;
use App\Services\InstallmentService;
use PHPUnit\Framework\TestCase;

class InstallmentTest extends TestCase
{
    public function test_total_contains_principal_interest_and_penalty(): void
    {
        $service = new InstallmentService($this->createMock(InstallmentRepositoryInterface::class), $this->createMock(LoanRepositoryInterface::class));

        $this->assertSame(435000.0, $service->total(400000, 10000, 25000));
    }
}
