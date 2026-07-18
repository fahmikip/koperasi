<?php

namespace App\Repositories\Contracts;

use App\Models\Saving;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SavingRepositoryInterface
{
    public function paginate(array $filters = []): LengthAwarePaginator;

    public function summary(array $filters = []): object;

    public function balance(int $memberId, int $savingTypeId): string;

    public function activeMembers(): Collection;

    public function savingTypes(bool $activeOnly = false): Collection;

    public function ledger(int $memberId, int $savingTypeId, array $excludedIds = [], bool $lock = false): Collection;

    public function nextTransactionNumber(): string;

    public function save(Saving $saving): Saving;

    public function delete(Saving $saving): void;
}
