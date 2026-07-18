<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardPrintHistory extends Model
{
    protected $fillable = ['member_id', 'printed_by', 'quantity', 'action', 'printed_at'];

    protected function casts(): array
    {
        return ['printed_at' => 'datetime'];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function printer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'printed_by');
    }
}
