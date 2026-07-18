<?php

namespace App\Services;

use App\Models\Installment;
use App\Models\Loan;
use App\Repositories\Contracts\InstallmentRepositoryInterface;
use App\Repositories\Contracts\LoanRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InstallmentService
{
    public function __construct(private InstallmentRepositoryInterface $installments, private LoanRepositoryInterface $loans) {}

    public function pay(Loan $loan, array $data, int $userId): Installment
    {
        return DB::transaction(function () use ($loan, $data, $userId): Installment {
            $loan = $this->installments->lockLoan($loan->id);
            if ($loan->status !== Loan::STATUS_DISBURSED) {
                throw ValidationException::withMessages(['loan' => 'Pembayaran hanya dapat dicatat untuk pinjaman yang sudah dicairkan dan belum lunas.']);
            }
            if ($data['paid_at'] < $loan->disbursed_at->format('Y-m-d')) {
                throw ValidationException::withMessages(['paid_at' => 'Tanggal pembayaran tidak boleh sebelum tanggal pencairan.']);
            }

            $principal = $this->toCents($data['principal_paid']);
            $interest = $this->toCents($data['interest_paid']);
            $penalty = $this->toCents($data['penalty']);
            $principalBalance = $this->toCents($loan->remaining_balance);
            $interestBalance = $this->toCents($loan->total_interest) - $this->toCents($this->installments->paidInterest($loan->id));

            if ($principal > $principalBalance) {
                throw ValidationException::withMessages(['principal_paid' => 'Pembayaran pokok melebihi sisa pokok pinjaman.']);
            }
            if ($interest > $interestBalance) {
                throw ValidationException::withMessages(['interest_paid' => 'Pembayaran bunga melebihi sisa bunga pinjaman.']);
            }

            $installment = new Installment($data);
            $installment->loan_id = $loan->id;
            $installment->received_by = $userId;
            $installment->installment_number = $this->installments->nextInstallmentNumber($loan->id);
            $installment->payment_number = $this->installments->nextPaymentNumber();
            $installment->total_paid = ($principal + $interest + $penalty) / 100;
            $installment = $this->installments->save($installment);

            $loan->remaining_balance = ($principalBalance - $principal) / 100;
            if (($principalBalance - $principal) === 0 && ($interestBalance - $interest) === 0) {
                $loan->status = Loan::STATUS_PAID;
            }
            $this->loans->save($loan);

            return $installment;
        });
    }

    public function total(float $principal, float $interest, float $penalty): float
    {
        return round($principal + $interest + $penalty, 2);
    }

    private function toCents(float|string $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }
}
