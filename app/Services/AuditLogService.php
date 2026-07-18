<?php

namespace App\Services;

use App\Models\Installment;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Saving;
use Spatie\Activitylog\Models\Activity;

class AuditLogService
{
    public function eventLabel(Activity $activity): string
    {
        if (in_array($activity->description, ['login', 'logout'], true)) {
            return $activity->description === 'login' ? 'Masuk Sistem' : 'Keluar Sistem';
        }

        return ['created' => 'Dibuat', 'updated' => 'Diperbarui', 'deleted' => 'Dihapus'][$activity->event] ?? 'Aktivitas';
    }

    public function subjectLabel(?string $type): string
    {
        return match ($type) {
            Member::class => 'Anggota',
            Saving::class => 'Simpanan',
            Loan::class => 'Pinjaman',
            Installment::class => 'Angsuran',
            default => 'Sistem',
        };
    }

    public function subjectIdentity(Activity $activity): string
    {
        $subject = $activity->subject;
        if (! $subject) {
            return '-';
        }

        return $subject->member_number ?? $subject->transaction_number ?? $subject->loan_number ?? $subject->payment_number ?? '#'.$subject->getKey();
    }

    public function changes(Activity $activity): array
    {
        $attributes = $activity->properties->get('attributes', []);
        $old = $activity->properties->get('old', []);

        return collect(array_unique([...array_keys($old), ...array_keys($attributes)]))->map(fn (string $key) => ['field' => $key, 'old' => $old[$key] ?? null, 'new' => $attributes[$key] ?? null])->all();
    }

    public function fieldLabel(string $field): string
    {
        return str($field)->replace('_', ' ')->title()->toString();
    }

    public function displayValue(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        if (is_bool($value)) {
            return $value ? 'Ya' : 'Tidak';
        }

        return is_scalar($value) ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
