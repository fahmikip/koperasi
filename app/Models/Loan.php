<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    protected $fillable = ['loan_number', 'member_id', 'approved_by', 'principal_amount', 'interest_rate', 'term_months', 'total_interest', 'total_payable', 'remaining_balance', 'status', 'applied_at', 'approved_at', 'disbursed_at', 'purpose', 'notes'];

    protected function casts(): array
    {
        return ['principal_amount' => 'decimal:2', 'total_interest' => 'decimal:2', 'total_payable' => 'decimal:2', 'remaining_balance' => 'decimal:2', 'applied_at' => 'date', 'approved_at' => 'date', 'disbursed_at' => 'date'];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class);
    }
}
