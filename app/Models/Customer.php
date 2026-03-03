<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'partner_id',
        'name',
        'trade_name',
        'document',
        'document_type',
        'email',
        'phone',
        'contact_name',
        'address',
        'city',
        'state',
        'zipcode',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function getDocumentFormattedAttribute(): string
    {
        $doc = preg_replace('/\D/', '', $this->document ?? '');
        if ($this->document_type === 'cpf' && strlen($doc) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $doc);
        }
        if ($this->document_type === 'cnpj' && strlen($doc) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $doc);
        }
        return $this->document ?? '';
    }

    public function getActiveProjectsCountAttribute(): int
    {
        return $this->projects()->where('status', 'active')->count();
    }

    public function getMonthlyValueAttribute(): float
    {
        return $this->projects()->where('status', 'active')->sum('monthly_value');
    }
}
