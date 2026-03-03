<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Partner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'distributor_id',
        'company_name',
        'trade_name',
        'cnpj',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'state',
        'zipcode',
        'logo',
        'proposal_header_color',
        'proposal_footer_text',
        'proposal_terms',
        'commission_model',
        'commission_rate',
        'commission_min',
        'commission_max',
        'currency',
        'locale',
        'is_active',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'commission_rate' => 'decimal:2',
        'commission_min'  => 'decimal:2',
        'commission_max'  => 'decimal:2',
    ];

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo
            ? Storage::url($this->logo)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->trade_name ?? $this->company_name) . '&color=ffffff&background=1e40af&size=64&bold=true';
    }

    public function getCommissionLabelAttribute(): string
    {
        if ($this->commission_model === 'fixed') {
            return number_format($this->commission_rate, 1) . '% (fixo)';
        }
        return number_format($this->commission_min, 1) . '% - ' . number_format($this->commission_max, 1) . '% (variavel)';
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->trade_name ?? $this->company_name;
    }
}
