<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Distributor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'trade_name',
        'document',
        'email',
        'phone',
        'contact_name',
        'commission_pct',
        'currency',
        'locale',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'commission_pct' => 'decimal:2',
        'is_active'      => 'boolean',
    ];

    public function partners(): HasMany
    {
        return $this->hasMany(Partner::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->trade_name ?? $this->name;
    }
}
