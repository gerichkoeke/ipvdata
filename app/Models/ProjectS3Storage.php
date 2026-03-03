<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectS3Storage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'project_s3_storage';

    protected $fillable = [
        'project_id',
        'name',
        'unit',
        'quantity',
        'quantity_gb',
        'price_per_gb',
        'price_total_monthly',
        'status',
        'discount_amount',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'quantity_gb' => 'decimal:2',
        'price_per_gb' => 'decimal:2',
        'price_total_monthly' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Calcula o custo total mensal (sem desconto)
     */
    public function getTotalMonthlyCost(): float
    {
        return (float) $this->price_total_monthly;
    }

    /**
     * Calcula o custo total mensal (com desconto)
     */
    public function getTotalMonthlyCostWithDiscount(): float
    {
        return max(0, $this->getTotalMonthlyCost() - $this->discount_amount);
    }
}
