<?php

namespace App\Services;

use App\Models\Member;
use App\Repositories\Contracts\MemberRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MemberService
{
    public function __construct(private MemberRepositoryInterface $members, private PhotoOptimizer $photos) {}

    public function create(array $data, ?UploadedFile $photo): Member
    {
        return DB::transaction(function () use ($data, $photo) {
            $data['member_number'] = $this->nextNumber();
            $data['qr_token'] = (string) Str::uuid();
            $data['photo_path'] = $photo ? $this->photos->store($photo) : null;

            return $this->members->create($data);
        });
    }

    public function update(Member $member, array $data, ?UploadedFile $photo): Member
    {
        return DB::transaction(function () use ($member, $data, $photo) {
            if ($photo) {
                $oldPhoto = $member->photo_path;
                $data['photo_path'] = $this->photos->store($photo);
                if ($oldPhoto) {
                    DB::afterCommit(fn () => Storage::disk(config('member_photos.disk', 'public'))->delete($oldPhoto));
                }
            }

            return $this->members->update($member, $data);
        });
    }

    public function delete(Member $member): void
    {
        DB::transaction(fn () => $this->members->delete($member));
    }

    private function nextNumber(): string
    {
        $year = now()->year;
        $prefix = "KOP-{$year}-";
        $last = Member::withTrashed()->where('member_number', 'like', "{$prefix}%")->lockForUpdate()->orderByDesc('member_number')->value('member_number');

        return $prefix.str_pad((string) ($last ? ((int) substr($last, -6)) + 1 : 1), 6, '0', STR_PAD_LEFT);
    }
}
