<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ReportRepositoryInterface
{
    public function members(array $filters): Collection;

    public function savings(array $filters): Collection;

    public function loans(array $filters): Collection;

    public function installments(array $filters): Collection;

    public function savingTypes(): Collection;
}
