<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Saving;
use App\Repositories\Contracts\SavingRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SavingService
{
    public function __construct(private SavingRepositoryInterface $savings) {}

    public function create(array $data, int $userId): Saving
    {
        return DB::transaction(function () use ($data, $userId): Saving {
            $this->lockMember((int) $data['member_id']);

            $saving = new Saving($data);
            $saving->transaction_number = $this->savings->nextTransactionNumber();
            $saving->created_by = $userId;

            $this->ensureNonNegativeBalance($saving);

            return $this->savings->save($saving);
        });
    }

    public function update(Saving $saving, array $data): Saving
    {
        return DB::transaction(function () use ($saving, $data): Saving {
            $memberIds = collect([$saving->member_id, (int) $data['member_id']])->unique()->sort();
            $memberIds->each(fn (int $id) => $this->lockMember($id));

            $saving->fill($data);
            $this->ensureNonNegativeBalance($saving);

            return $this->savings->save($saving);
        });
    }

    public function delete(Saving $saving): void
    {
        DB::transaction(function () use ($saving): void {
            $this->lockMember($saving->member_id);
            $this->ensureNonNegativeBalance(null, $saving);
            $this->savings->delete($saving);
        });
    }

    private function lockMember(int $memberId): void
    {
        Member::query()->whereKey($memberId)->lockForUpdate()->firstOrFail();
    }

    private function ensureNonNegativeBalance(?Saving $candidate, ?Saving $removed = null): void
    {
        $pairs = collect();
        if ($candidate) {
            $pairs->push([$candidate->member_id, $candidate->saving_type_id]);
        }
        if ($candidate?->exists) {
            $original = $candidate->getOriginal();
            $pairs->push([$original['member_id'], $original['saving_type_id']]);
        }
        if ($removed) {
            $pairs->push([$removed->member_id, $removed->saving_type_id]);
        }

        $pairs->unique(fn (array $pair) => implode(':', $pair))->each(function (array $pair) use ($candidate, $removed): void {
            $excludedIds = collect([$candidate?->exists ? $candidate->getKey() : null, $removed?->getKey()])->filter()->values()->all();
            $entries = $this->savings->ledger((int) $pair[0], (int) $pair[1], $excludedIds, lock: true);

            if ($candidate && (int) $candidate->member_id === (int) $pair[0] && (int) $candidate->saving_type_id === (int) $pair[1]) {
                $entries->push($candidate);
            }

            $this->validateLedger($entries);
        });
    }

    private function validateLedger(Collection $entries): void
    {
        $balance = 0;
        $entries->sortBy(fn (Saving $entry) => sprintf('%s-%020d', $entry->transaction_date instanceof \DateTimeInterface ? $entry->transaction_date->format('Y-m-d') : $entry->transaction_date, $entry->exists ? $entry->id : PHP_INT_MAX))
            ->each(function (Saving $entry) use (&$balance): void {
                $balance += $entry->signedAmountInCents();
                if ($balance < 0) {
                    throw ValidationException::withMessages([
                        'amount' => 'Transaksi membuat saldo simpanan menjadi negatif pada '.$entry->transaction_date->format('d-m-Y').'.',
                    ]);
                }
            });
    }
}
