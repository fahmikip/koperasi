<?php

namespace App\Repositories\Contracts;

use App\Models\Loan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface LoanRepositoryInterface
{
    public function paginate(array $filters = []): LengthAwarePaginator;

    public function summary(): object;

    public function activeMembers(): Collection;

    public function nextLoanNumber(): string;

    public function save(Loan $loan): Loan;

    public function delete(Loan $loan): void;

    public function lock(int $loanId): Loan;
}
