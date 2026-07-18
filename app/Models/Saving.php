<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Saving extends Model
{
    protected $fillable = ['transaction_number', 'member_id', 'saving_type_id', 'created_by', 'transaction_date', 'direction', 'amount', 'notes'];

    protected function casts(): array
    {
        return ['transaction_date' => 'date', 'amount' => 'decimal:2'];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(SavingType::class, 'saving_type_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
