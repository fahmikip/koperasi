<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;

class MemberPolicy
{
    public function before(User $u): ?bool
    {
        return $u->hasRole('Super Admin') ? true : null;
    }

    public function viewAny(User $u): bool
    {
        return $u->can('members.view');
    }

    public function view(User $u, Member $m): bool
    {
        return $u->can('members.view') || $u->member?->is($m);
    }

    public function create(User $u): bool
    {
        return $u->can('members.create');
    }

    public function update(User $u, Member $m): bool
    {
        return $u->can('members.update');
    }

    public function delete(User $u, Member $m): bool
    {
        return $u->can('members.delete');
    }
}
