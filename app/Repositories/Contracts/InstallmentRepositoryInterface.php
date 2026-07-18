<?php

namespace App\Repositories\Contracts;

use App\Models\Installment;
use App\Models\Loan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface InstallmentRepositoryInterface
{
    public function paginate(array $filters = []): LengthAwarePaginator;

    public function paidInterest(int $loanId): string;

    public function nextInstallmentNumber(int $loanId): int;

    public function nextPaymentNumber(): string;

    public function save(Installment $installment): Installment;

    public function lockLoan(int $loanId): Loan;
}
