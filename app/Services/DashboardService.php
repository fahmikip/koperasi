<?php

namespace App\Services;

use App\Models\Installment;
use App\Models\Loan;
use App\Models\Saving;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class DashboardService
{
    public function chart(array $filters): array
    {
        [$from, $to, $period] = $this->resolvePeriod($filters);
        $grouping = $from->diffInDays($to) <= 31 ? 'day' : ($from->diffInDays($to) <= 120 ? 'week' : 'month');
        $buckets = $this->buckets($from, $to, $grouping);

        $savings = Saving::query()->whereBetween('transaction_date', [$from->toDateString(), $to->toDateString()])->get();
        $loans = Loan::query()->whereNotNull('disbursed_at')->whereBetween('disbursed_at', [$from->toDateString(), $to->toDateString()])->get();
        $installments = Installment::query()->whereBetween('paid_at', [$from->toDateString(), $to->toDateString()])->get();

        return [
            'period' => $period,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'caption' => $from->translatedFormat('d M Y').' – '.$to->translatedFormat('d M Y'),
            'labels' => $buckets->pluck('label')->values(),
            'datasets' => [
                'deposits' => $this->totals($buckets, $savings->where('direction', 'deposit'), 'transaction_date', 'amount', $grouping),
                'withdrawals' => $this->totals($buckets, $savings->where('direction', 'withdrawal'), 'transaction_date', 'amount', $grouping),
                'loans' => $this->totals($buckets, $loans, 'disbursed_at', 'principal_amount', $grouping),
                'installments' => $this->totals($buckets, $installments, 'paid_at', 'total_paid', $grouping),
            ],
        ];
    }

    private function resolvePeriod(array $filters): array
    {
        $period = $filters['period'] ?? '30_days';
        $to = today();
        $from = match ($period) {
            '7_days' => $to->copy()->subDays(6),
            '90_days' => $to->copy()->subDays(89),
            'year' => $to->copy()->startOfYear(),
            'custom' => Carbon::parse($filters['date_from'])->startOfDay(),
            default => $to->copy()->subDays(29),
        };

        if ($period === 'custom') {
            $to = Carbon::parse($filters['date_to'])->endOfDay();
        }

        return [$from, $to, $period];
    }

    private function buckets(Carbon $from, Carbon $to, string $grouping): Collection
    {
        $step = match ($grouping) {
            'week' => '1 week',
            'month' => '1 month',
            default => '1 day',
        };

        return collect(CarbonPeriod::create($from, $step, $to))->map(fn (Carbon $date): array => [
            'key' => $this->key($date, $grouping),
            'label' => match ($grouping) {
                'month' => $date->translatedFormat('M Y'),
                'week' => $date->translatedFormat('d M'),
                default => $date->translatedFormat('d M'),
            },
        ])->unique('key')->values();
    }

    private function totals(Collection $buckets, Collection $rows, string $dateField, string $amountField, string $grouping): array
    {
        $totals = $rows->groupBy(fn ($row): string => $this->key(Carbon::parse($row->{$dateField}), $grouping))
            ->map(fn (Collection $items): float => (float) $items->sum($amountField));

        return $buckets->map(fn (array $bucket): float => $totals->get($bucket['key'], 0))->values()->all();
    }

    private function key(Carbon $date, string $grouping): string
    {
        return match ($grouping) {
            'week' => $date->copy()->startOfWeek()->format('Y-m-d'),
            'month' => $date->format('Y-m'),
            default => $date->format('Y-m-d'),
        };
    }
}
