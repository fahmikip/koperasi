<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Installment extends Model
{
    use LogsActivity;

    protected $fillable = ['payment_number', 'loan_id', 'received_by', 'installment_number', 'paid_at', 'principal_paid', 'interest_paid', 'penalty', 'total_paid', 'notes'];

    protected function casts(): array
    {
        return ['paid_at' => 'date', 'principal_paid' => 'decimal:2', 'interest_paid' => 'decimal:2', 'penalty' => 'decimal:2', 'total_paid' => 'decimal:2'];
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }
}
