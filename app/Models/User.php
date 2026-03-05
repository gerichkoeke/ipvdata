<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'panel',
        'locale',
        'currency',
        'partner_id',
        'distributor_id',
        'is_active',
        'mfa_enabled',
        'mfa_secret',
        'mfa_confirmed_at',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'mfa_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'mfa_confirmed_at'  => 'datetime',
        'last_login_at'     => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
        'mfa_enabled'       => 'boolean',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        $isActive = $this->is_active === null ? true : (bool) $this->is_active;

        return match($panel->getId()) {
            'admin'       => $this->panel === 'admin'       && $isActive,
            'partner'     => $this->panel === 'partner'     && $isActive,
            'distributor' => $this->panel === 'distributor' && $isActive,
            default       => false,
        };
    }

    public function getFilamentName(): string
    {
        $parts = explode(' ', trim($this->name));
        return $parts[0] ?? $this->name;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar
            ? Storage::url($this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=ffffff&background=4f46e5&size=64';
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function getActiveCurrencyAttribute(): string
    {
        if ($this->partner_id && $this->partner) {
            return $this->partner->currency ?? $this->currency ?? 'BRL';
        }
        if ($this->distributor_id && $this->distributor) {
            return $this->distributor->currency ?? $this->currency ?? 'BRL';
        }
        return $this->currency ?? 'BRL';
    }

    public function getCurrencySymbolAttribute(): string
    {
        return match($this->active_currency) {
            'USD' => 'US$',
            'PYG' => '₲',
            default => 'R$',
        };
    }

    public function getActiveLocaleAttribute(): string
    {
        if ($this->partner_id && $this->partner) {
            return $this->partner->locale ?? $this->locale ?? 'pt_BR';
        }
        if ($this->distributor_id && $this->distributor) {
            return $this->distributor->locale ?? $this->locale ?? 'pt_BR';
        }
        return $this->locale ?? 'pt_BR';
    }
}
