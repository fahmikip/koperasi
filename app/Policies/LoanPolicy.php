<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;

class LoanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('loans.view');
    }

    public function view(User $user, Loan $loan): bool
    {
        return $user->can('loans.view') || $user->member?->is($loan->member);
    }

    public function create(User $user): bool
    {
        return $user->can('loans.create') || $user->can('loans.manage');
    }

    public function update(User $user, Loan $loan): bool
    {
        return $loan->canBeModified() && ($user->can('loans.create') || $user->can('loans.manage'));
    }

    public function delete(User $user, Loan $loan): bool
    {
        return $this->update($user, $loan);
    }

    public function review(User $user, Loan $loan): bool
    {
        return $loan->canBeReviewed() && ($user->can('loans.approve') || $user->can('loans.manage'));
    }

    public function disburse(User $user, Loan $loan): bool
    {
        return $loan->canBeDisbursed() && ($user->can('loans.disburse') || $user->can('loans.manage'));
    }
}
