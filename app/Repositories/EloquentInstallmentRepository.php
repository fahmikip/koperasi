<?php

namespace App\Repositories;

use App\Models\Installment;
use App\Models\Loan;
use App\Repositories\Contracts\InstallmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class EloquentInstallmentRepository implements InstallmentRepositoryInterface
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return Installment::query()->with(['loan.member', 'receiver'])
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->where(fn (Builder $query) => $query
                ->where('payment_number', 'like', "%{$search}%")
                ->orWhereHas('loan', fn (Builder $query) => $query->where('loan_number', 'like', "%{$search}%")
                    ->orWhereHas('member', fn (Builder $query) => $query->where('name', 'like', "%{$search}%")))))
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('paid_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('paid_at', '<=', $date))
            ->latest('paid_at')->latest('id')->paginate(15)->withQueryString();
    }

    public function paidInterest(int $loanId): string
    {
        return (string) Installment::query()->where('loan_id', $loanId)->sum('interest_paid');
    }

    public function nextInstallmentNumber(int $loanId): int
    {
        return ((int) Installment::query()->where('loan_id', $loanId)->max('installment_number')) + 1;
    }

    public function nextPaymentNumber(): string
    {
        do {
            $number = 'PAY-'.now()->format('Ymd').'-'.Str::upper(Str::random(8));
        } while (Installment::query()->where('payment_number', $number)->exists());

        return $number;
    }

    public function save(Installment $installment): Installment
    {
        $installment->save();

        return $installment->load(['loan.member', 'receiver']);
    }

    public function lockLoan(int $loanId): Loan
    {
        return Loan::query()->whereKey($loanId)->lockForUpdate()->firstOrFail();
    }
}
