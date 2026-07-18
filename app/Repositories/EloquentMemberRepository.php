<?php

namespace App\Repositories;

use App\Models\Member;
use App\Repositories\Contracts\MemberRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentMemberRepository implements MemberRepositoryInterface
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return Member::query()->when($filters['search'] ?? null, fn ($q, $s) => $q->where(fn ($q) => $q->where('name', 'like', "%{$s}%")->orWhere('member_number', 'like', "%{$s}%")->orWhere('nik', 'like', "%{$s}%")))->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))->latest()->paginate(15)->withQueryString();
    }

    public function create(array $data): Member
    {
        return Member::create($data);
    }

    public function update(Member $member, array $data): Member
    {
        $member->update($data);

        return $member->refresh();
    }

    public function delete(Member $member): void
    {
        $member->delete();
    }
}
