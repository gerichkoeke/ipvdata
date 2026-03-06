<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
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
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        if (filter_var($this->logo, FILTER_VALIDATE_URL)) {
            return $this->logo;
        }

        $normalizedPath = ltrim($this->logo, '/');

        if (str_starts_with($normalizedPath, 'storage/')) {
            return asset($normalizedPath);
        }

        if (file_exists(public_path($normalizedPath))) {
            return asset($normalizedPath);
        }

        return Storage::url($normalizedPath);
    }

}

