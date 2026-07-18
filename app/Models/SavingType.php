<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SavingType extends Model
{
    protected $fillable = ['name', 'frequency', 'default_amount', 'is_active'];

    protected function casts(): array
    {
        return ['default_amount' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public function savings(): HasMany
    {
        return $this->hasMany(Saving::class);
    }
}
