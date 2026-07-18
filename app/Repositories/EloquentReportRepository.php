<?php

namespace App\Repositories;

use App\Models\Installment;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Saving;
use App\Models\SavingType;
use App\Repositories\Contracts\ReportRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentReportRepository implements ReportRepositoryInterface
{
    public function members(array $filters): Collection
    {
        return Member::query()
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->where(fn (Builder $query) => $query->where('name', 'like', "%{$search}%")->orWhere('member_number', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%")))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => in_array($status, ['active', 'inactive']) ? $query->where('status', $status) : $query)
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('joined_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('joined_at', '<=', $date))
            ->orderBy('name')->get();
    }

    public function savings(array $filters): Collection
    {
        return Saving::query()->with(['member', 'type'])
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->where(fn (Builder $query) => $query->where('transaction_number', 'like', "%{$search}%")->orWhereHas('member', fn (Builder $query) => $query->where('name', 'like', "%{$search}%")->orWhere('member_number', 'like', "%{$search}%"))))
            ->when($filters['direction'] ?? null, fn (Builder $query, string $direction) => $query->where('direction', $direction))
            ->when($filters['saving_type_id'] ?? null, fn (Builder $query, int|string $type) => $query->where('saving_type_id', $type))
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('transaction_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('transaction_date', '<=', $date))
            ->orderByDesc('transaction_date')->orderByDesc('id')->get();
    }

    public function loans(array $filters): Collection
    {
        return Loan::query()->with(['member', 'approver'])
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->where(fn (Builder $query) => $query->where('loan_number', 'like', "%{$search}%")->orWhereHas('member', fn (Builder $query) => $query->where('name', 'like', "%{$search}%")->orWhere('member_number', 'like', "%{$search}%"))))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => in_array($status, ['submitted', 'approved', 'rejected', 'disbursed', 'paid']) ? $query->where('status', $status) : $query)
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('applied_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('applied_at', '<=', $date))
            ->orderByDesc('applied_at')->orderByDesc('id')->get();
    }

    public function installments(array $filters): Collection
    {
        return Installment::query()->with(['loan.member', 'receiver'])
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->where(fn (Builder $query) => $query->where('payment_number', 'like', "%{$search}%")->orWhereHas('loan', fn (Builder $query) => $query->where('loan_number', 'like', "%{$search}%")->orWhereHas('member', fn (Builder $query) => $query->where('name', 'like', "%{$search}%")))))
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('paid_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('paid_at', '<=', $date))
            ->orderByDesc('paid_at')->orderByDesc('id')->get();
    }

    public function savingTypes(): Collection
    {
        return SavingType::query()->orderBy('name')->get(['id', 'name']);
    }
}
