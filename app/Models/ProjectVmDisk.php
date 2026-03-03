<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectVmDisk extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_vm_id',
        'size_gb',
        'disk_type_id',
        'discount_amount',
    ];

    protected $casts = [
        'size_gb' => 'integer',
        'discount_amount' => 'decimal:2',
    ];

    public function vm(): BelongsTo
    {
        return $this->belongsTo(ProjectVm::class, 'project_vm_id');
    }

    public function diskType(): BelongsTo
    {
        return $this->belongsTo(DiskType::class);
    }

    /**
     * Calcula o custo mensal do disco (sem desconto)
     */
    public function getTotalMonthlyCost(): float
    {
        if (!$this->diskType) {
            return 0;
        }

        return round($this->size_gb * $this->diskType->price_per_gb, 2);
    }

    /**
     * Calcula o custo mensal do disco (com desconto)
     */
    public function getTotalMonthlyCostWithDiscount(): float
    {
        return max(0, $this->getTotalMonthlyCost() - $this->discount_amount);
    }
}
