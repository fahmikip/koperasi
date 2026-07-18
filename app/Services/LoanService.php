<?php

namespace App\Services;

use App\Models\Loan;
use App\Repositories\Contracts\LoanRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanService
{
    public function __construct(private LoanRepositoryInterface $loans) {}

    public function submit(array $data): Loan
    {
        return DB::transaction(function () use ($data): Loan {
            $loan = new Loan($this->withCalculatedAmounts($data));
            $loan->loan_number = $this->loans->nextLoanNumber();
            $loan->status = Loan::STATUS_SUBMITTED;
            $loan->remaining_balance = 0;

            return $this->loans->save($loan);
        });
    }

    public function update(Loan $loan, array $data): Loan
    {
        return DB::transaction(function () use ($loan, $data): Loan {
            $loan = $this->loans->lock($loan->id);
            $this->ensureStatus($loan, Loan::STATUS_SUBMITTED, 'Hanya pengajuan yang belum ditinjau dapat diubah.');
            $loan->fill($this->withCalculatedAmounts($data));

            return $this->loans->save($loan);
        });
    }

    public function delete(Loan $loan): void
    {
        DB::transaction(function () use ($loan): void {
            $loan = $this->loans->lock($loan->id);
            $this->ensureStatus($loan, Loan::STATUS_SUBMITTED, 'Hanya pengajuan yang belum ditinjau dapat dihapus.');
            $this->loans->delete($loan);
        });
    }

    public function approve(Loan $loan, int $userId, ?string $notes = null): Loan
    {
        return DB::transaction(function () use ($loan, $userId, $notes): Loan {
            $loan = $this->loans->lock($loan->id);
            $this->ensureStatus($loan, Loan::STATUS_SUBMITTED, 'Pinjaman sudah ditinjau dan tidak dapat disetujui ulang.');
            $loan->status = Loan::STATUS_APPROVED;
            $loan->approved_by = $userId;
            $loan->approved_at = today();
            $loan->notes = $this->appendNote($loan->notes, 'Persetujuan', $notes);

            return $this->loans->save($loan);
        });
    }

    public function reject(Loan $loan, int $userId, string $notes): Loan
    {
        return DB::transaction(function () use ($loan, $userId, $notes): Loan {
            $loan = $this->loans->lock($loan->id);
            $this->ensureStatus($loan, Loan::STATUS_SUBMITTED, 'Pinjaman sudah ditinjau dan tidak dapat ditolak ulang.');
            $loan->status = Loan::STATUS_REJECTED;
            $loan->approved_by = $userId;
            $loan->approved_at = today();
            $loan->notes = $this->appendNote($loan->notes, 'Penolakan', $notes);

            return $this->loans->save($loan);
        });
    }

    public function disburse(Loan $loan, string $date, ?string $notes = null): Loan
    {
        return DB::transaction(function () use ($loan, $date, $notes): Loan {
            $loan = $this->loans->lock($loan->id);
            $this->ensureStatus($loan, Loan::STATUS_APPROVED, 'Hanya pinjaman yang sudah disetujui dapat dicairkan.');
            if ($date < $loan->approved_at->format('Y-m-d')) {
                throw ValidationException::withMessages(['disbursed_at' => 'Tanggal pencairan tidak boleh sebelum tanggal persetujuan.']);
            }
            $loan->status = Loan::STATUS_DISBURSED;
            $loan->disbursed_at = $date;
            $loan->remaining_balance = $loan->total_payable;
            $loan->notes = $this->appendNote($loan->notes, 'Pencairan', $notes);

            return $this->loans->save($loan);
        });
    }

    public function calculate(float $principal, float $monthlyRate, int $termMonths): array
    {
        $interest = round($principal * ($monthlyRate / 100) * $termMonths, 2);

        return ['total_interest' => $interest, 'total_payable' => round($principal + $interest, 2)];
    }

    private function withCalculatedAmounts(array $data): array
    {
        return array_merge($data, $this->calculate((float) $data['principal_amount'], (float) $data['interest_rate'], (int) $data['term_months']));
    }

    private function ensureStatus(Loan $loan, string $expected, string $message): void
    {
        if ($loan->status !== $expected) {
            throw ValidationException::withMessages(['status' => $message]);
        }
    }

    private function appendNote(?string $current, string $label, ?string $note): ?string
    {
        if (! $note) {
            return $current;
        }

        return trim(collect([$current, "{$label}: {$note}"])->filter()->implode("\n"));
    }
}
