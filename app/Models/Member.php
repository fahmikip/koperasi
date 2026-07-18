<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Member extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = ['user_id', 'member_number', 'nik', 'name', 'birth_place', 'birth_date', 'gender', 'address', 'district', 'regency', 'province', 'whatsapp', 'email', 'occupation', 'photo_path', 'joined_at', 'valid_until', 'status', 'qr_token'];

    protected function casts(): array
    {
        return ['birth_date' => 'date', 'joined_at' => 'date', 'valid_until' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function savings(): HasMany
    {
        return $this->hasMany(Saving::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function cardPrintHistories(): HasMany
    {
        return $this->hasMany(CardPrintHistory::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }
}
