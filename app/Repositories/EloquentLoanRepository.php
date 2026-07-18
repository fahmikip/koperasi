<?php

namespace App\Repositories;

use App\Models\Loan;
use App\Models\Member;
use App\Repositories\Contracts\LoanRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentLoanRepository implements LoanRepositoryInterface
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return Loan::query()->with(['member', 'approver'])
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->where(fn (Builder $query) => $query
                ->where('loan_number', 'like', "%{$search}%")
                ->orWhereHas('member', fn (Builder $query) => $query->where('name', 'like', "%{$search}%")->orWhere('member_number', 'like', "%{$search}%"))))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->latest('applied_at')->latest('id')->paginate(15)->withQueryString();
    }

    public function summary(): object
    {
        return Loan::query()->selectRaw("COUNT(*) as total, COALESCE(SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END), 0) as submitted, COALESCE(SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END), 0) as approved, COALESCE(SUM(CASE WHEN status = 'disbursed' THEN remaining_balance ELSE 0 END), 0) as outstanding")->firstOrFail();
    }

    public function activeMembers(): Collection
    {
        return Member::query()->where('status', 'active')->orderBy('name')->get(['id', 'member_number', 'name']);
    }

    public function nextLoanNumber(): string
    {
        DB::table('members')->orderBy('id')->lockForUpdate()->value('id');
        $prefix = 'LOAN-'.now()->format('Y').'-';
        $last = Loan::query()->where('loan_number', 'like', $prefix.'%')->lockForUpdate()->orderByDesc('loan_number')->value('loan_number');

        return $prefix.str_pad((string) ($last ? ((int) substr($last, -6)) + 1 : 1), 6, '0', STR_PAD_LEFT);
    }

    public function save(Loan $loan): Loan
    {
        $loan->save();

        return $loan->load(['member', 'approver']);
    }

    public function delete(Loan $loan): void
    {
        $loan->delete();
    }

    public function lock(int $loanId): Loan
    {
        return Loan::query()->whereKey($loanId)->lockForUpdate()->firstOrFail();
    }
}
