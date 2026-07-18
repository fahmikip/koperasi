<?php

namespace App\Repositories;

use App\Models\Member;
use App\Models\Saving;
use App\Models\SavingType;
use App\Repositories\Contracts\SavingRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentSavingRepository implements SavingRepositoryInterface
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->filteredQuery($filters)
            ->with(['member', 'type', 'creator'])
            ->latest('transaction_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();
    }

    public function summary(array $filters = []): object
    {
        return $this->filteredQuery($filters)
            ->selectRaw("COALESCE(SUM(CASE WHEN direction = 'deposit' THEN amount ELSE -amount END), 0) as balance, COALESCE(SUM(CASE WHEN direction = 'deposit' THEN amount ELSE 0 END), 0) as deposits, COALESCE(SUM(CASE WHEN direction = 'withdrawal' THEN amount ELSE 0 END), 0) as withdrawals")
            ->firstOrFail();
    }

    public function balance(int $memberId, int $savingTypeId): string
    {
        return (string) Saving::query()
            ->where('member_id', $memberId)
            ->where('saving_type_id', $savingTypeId)
            ->selectRaw("COALESCE(SUM(CASE WHEN direction = 'deposit' THEN amount ELSE -amount END), 0) as balance")
            ->value('balance');
    }

    public function activeMembers(): Collection
    {
        return Member::query()->where('status', 'active')->orderBy('name')->get(['id', 'member_number', 'name']);
    }

    public function savingTypes(bool $activeOnly = false): Collection
    {
        return SavingType::query()->when($activeOnly, fn (Builder $query) => $query->where('is_active', true))->orderBy('name')->get();
    }

    public function ledger(int $memberId, int $savingTypeId, array $excludedIds = [], bool $lock = false): Collection
    {
        return Saving::query()
            ->where('member_id', $memberId)
            ->where('saving_type_id', $savingTypeId)
            ->when($excludedIds, fn (Builder $query) => $query->whereNotIn('id', $excludedIds))
            ->when($lock, fn (Builder $query) => $query->lockForUpdate())
            ->get();
    }

    public function nextTransactionNumber(): string
    {
        DB::table('saving_types')->orderBy('id')->lockForUpdate()->value('id');
        $prefix = 'SAV-'.now()->format('Ymd').'-';
        $last = Saving::query()->where('transaction_number', 'like', $prefix.'%')->lockForUpdate()->orderByDesc('transaction_number')->value('transaction_number');

        return $prefix.str_pad((string) ($last ? ((int) substr($last, -5)) + 1 : 1), 5, '0', STR_PAD_LEFT);
    }

    public function save(Saving $saving): Saving
    {
        $saving->save();

        return $saving->load(['member', 'type', 'creator']);
    }

    public function delete(Saving $saving): void
    {
        $saving->delete();
    }

    private function filteredQuery(array $filters): Builder
    {
        return Saving::query()
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->where(fn (Builder $query) => $query
                ->where('transaction_number', 'like', "%{$search}%")
                ->orWhereHas('member', fn (Builder $query) => $query->where('name', 'like', "%{$search}%")->orWhere('member_number', 'like', "%{$search}%"))))
            ->when($filters['direction'] ?? null, fn (Builder $query, string $direction) => $query->where('direction', $direction))
            ->when($filters['saving_type_id'] ?? null, fn (Builder $query, string $type) => $query->where('saving_type_id', $type));
    }
}
