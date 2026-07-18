<?php

namespace App\Services;

use App\Models\Installment;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Saving;
use App\Repositories\Contracts\ReportRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ReportService
{
    public const TYPES = ['members', 'savings', 'loans', 'installments', 'transactions'];

    public function __construct(private ReportRepositoryInterface $reports) {}

    public function generate(string $type, array $filters): array
    {
        abort_unless(in_array($type, self::TYPES, true), 404);

        return match ($type) {
            'members' => $this->memberReport($filters),
            'savings' => $this->savingReport($filters),
            'loans' => $this->loanReport($filters),
            'installments' => $this->installmentReport($filters),
            'transactions' => $this->transactionReport($filters),
        };
    }

    public function paginate(Collection $rows, int $perPage = 20): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator($rows->forPage($page, $perPage)->values(), $rows->count(), $perPage, $page, ['path' => request()->url(), 'query' => request()->query()]);
    }

    private function memberReport(array $filters): array
    {
        $members = $this->reports->members($filters);

        return $this->result('Laporan Anggota', ['Nomor Anggota', 'Nama', 'NIK', 'WhatsApp', 'Bergabung', 'Berlaku Sampai', 'Status'], $members->map(fn (Member $member) => [$member->member_number, $member->name, $member->nik, $member->whatsapp, $member->joined_at->format('d-m-Y'), $member->valid_until->format('d-m-Y'), $member->status === 'active' ? 'Aktif' : 'Nonaktif']), [
            ['Total Anggota', number_format($members->count())],
            ['Anggota Aktif', number_format($members->where('status', 'active')->count())],
            ['Anggota Nonaktif', number_format($members->where('status', 'inactive')->count())],
        ]);
    }

    private function savingReport(array $filters): array
    {
        $savings = $this->reports->savings($filters);
        $deposits = $savings->where('direction', 'deposit')->sum('amount');
        $withdrawals = $savings->where('direction', 'withdrawal')->sum('amount');

        return $this->result('Laporan Simpanan', ['Tanggal', 'Nomor Transaksi', 'Anggota', 'Jenis Simpanan', 'Transaksi', 'Nominal'], $savings->map(fn (Saving $saving) => [$saving->transaction_date->format('d-m-Y'), $saving->transaction_number, $saving->member->name, $saving->type->name, $saving->direction === 'deposit' ? 'Setoran' : 'Penarikan', $this->currency($saving->amount)]), [
            ['Total Setoran', $this->currency($deposits)],
            ['Total Penarikan', $this->currency($withdrawals)],
            ['Saldo Bersih', $this->currency($deposits - $withdrawals)],
        ]);
    }

    private function loanReport(array $filters): array
    {
        $loans = $this->reports->loans($filters);

        return $this->result('Laporan Pinjaman', ['Tanggal', 'Nomor Pinjaman', 'Anggota', 'Pokok', 'Bunga', 'Total Tagihan', 'Sisa Pokok', 'Status'], $loans->map(fn (Loan $loan) => [$loan->applied_at->format('d-m-Y'), $loan->loan_number, $loan->member->name, $this->currency($loan->principal_amount), $this->currency($loan->total_interest), $this->currency($loan->total_payable), $this->currency($loan->remaining_balance), $this->loanStatus($loan->status)]), [
            ['Total Pengajuan', number_format($loans->count())],
            ['Total Pokok', $this->currency($loans->sum('principal_amount'))],
            ['Sisa Pokok Aktif', $this->currency($loans->where('status', 'disbursed')->sum('remaining_balance'))],
        ]);
    }

    private function installmentReport(array $filters): array
    {
        $installments = $this->reports->installments($filters);

        return $this->result('Laporan Angsuran', ['Tanggal', 'Nomor Pembayaran', 'Pinjaman', 'Anggota', 'Pokok', 'Bunga', 'Denda', 'Total'], $installments->map(fn (Installment $payment) => [$payment->paid_at->format('d-m-Y'), $payment->payment_number, $payment->loan->loan_number, $payment->loan->member->name, $this->currency($payment->principal_paid), $this->currency($payment->interest_paid), $this->currency($payment->penalty), $this->currency($payment->total_paid)]), [
            ['Pokok Terbayar', $this->currency($installments->sum('principal_paid'))],
            ['Pendapatan Bunga', $this->currency($installments->sum('interest_paid'))],
            ['Pendapatan Denda', $this->currency($installments->sum('penalty'))],
            ['Total Diterima', $this->currency($installments->sum('total_paid'))],
        ]);
    }

    private function transactionReport(array $filters): array
    {
        $savings = $this->reports->savings($filters)->map(fn (Saving $saving) => ['date' => $saving->transaction_date, 'row' => [$saving->transaction_date->format('d-m-Y'), $saving->transaction_number, $saving->member->name, 'Simpanan · '.$saving->type->name, $saving->direction === 'deposit' ? 'Masuk' : 'Keluar', $this->currency($saving->amount)], 'signed' => $saving->direction === 'deposit' ? (float) $saving->amount : -(float) $saving->amount]);
        $installments = ($filters['direction'] ?? null) === 'withdrawal' ? collect() : $this->reports->installments($filters)->map(fn (Installment $payment) => ['date' => $payment->paid_at, 'row' => [$payment->paid_at->format('d-m-Y'), $payment->payment_number, $payment->loan->member->name, 'Angsuran · '.$payment->loan->loan_number, 'Masuk', $this->currency($payment->total_paid)], 'signed' => (float) $payment->total_paid]);
        $transactions = $savings->concat($installments)->sortByDesc('date')->values();

        return $this->result('Laporan Seluruh Transaksi', ['Tanggal', 'Nomor Transaksi', 'Anggota', 'Kategori', 'Arus', 'Nominal'], $transactions->pluck('row'), [
            ['Jumlah Transaksi', number_format($transactions->count())],
            ['Total Arus Masuk', $this->currency($transactions->where('signed', '>=', 0)->sum('signed'))],
            ['Total Arus Keluar', $this->currency(abs($transactions->where('signed', '<', 0)->sum('signed')))],
            ['Arus Bersih', $this->currency($transactions->sum('signed'))],
        ]);
    }

    private function result(string $title, array $headings, Collection $rows, array $summary): array
    {
        return compact('title', 'headings', 'rows', 'summary');
    }

    private function currency(float|int|string $value): string
    {
        return 'Rp '.number_format((float) $value, 0, ',', '.');
    }

    private function loanStatus(string $status): string
    {
        return ['submitted' => 'Diajukan', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'disbursed' => 'Dicairkan', 'paid' => 'Lunas'][$status] ?? $status;
    }
}
