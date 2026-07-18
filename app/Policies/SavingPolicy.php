<?php

namespace App\Policies;

use App\Models\Saving;
use App\Models\User;

class SavingPolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasRole('Super Admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('savings.view');
    }

    public function view(User $user, Saving $saving): bool
    {
        return $user->can('savings.view') || $user->member?->is($saving->member);
    }

    public function create(User $user): bool
    {
        return $user->can('savings.manage');
    }

    public function update(User $user, Saving $saving): bool
    {
        return $user->can('savings.manage');
    }

    public function delete(User $user, Saving $saving): bool
    {
        return $user->can('savings.manage');
    }
}
