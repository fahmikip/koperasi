<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AuditLogRepositoryInterface
{
    public function paginate(array $filters = []): LengthAwarePaginator;

    public function summary(): array;

    public function users(): Collection;
}
