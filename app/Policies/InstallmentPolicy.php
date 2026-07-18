<?php

namespace App\Policies;

use App\Models\Installment;
use App\Models\Loan;
use App\Models\User;

class InstallmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('installments.view') || $user->can('installments.manage');
    }

    public function view(User $user, Installment $installment): bool
    {
        return $this->viewAny($user) || $user->member?->is($installment->loan->member);
    }

    public function create(User $user, Loan $loan): bool
    {
        return $loan->status === Loan::STATUS_DISBURSED && $user->can('installments.manage');
    }
}
