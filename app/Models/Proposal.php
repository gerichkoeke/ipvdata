<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'number',
        'partner_id',
        'customer_id',
        'created_by',
        'title',
        'description',
        'status',
        'subtotal',
        'discount',
        'total',
        'commission_value',
        'valid_until',
        'notes',
        'sent_at',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'valid_until'  => 'date',
        'sent_at'      => 'datetime',
        'approved_at'  => 'datetime',
        'subtotal'     => 'decimal:2',
        'discount'     => 'decimal:2',
        'total'        => 'decimal:2',
        'commission_value' => 'decimal:2',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(ProposalItem::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
