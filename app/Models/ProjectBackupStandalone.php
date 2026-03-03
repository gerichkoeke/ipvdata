<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectBackupStandalone extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'project_backup_standalone';

    protected $fillable = [
        'project_id',
        'name',
        'connection_type',
        'vm_count',
        'backup_retention_id',
        'backup_software_id',
        'storage_per_vm_gb',
        'total_storage_gb',
        'price_per_gb',
        'price_backup_software',
        'price_total_monthly',
        'status',
        'discount_amount',
    ];

    protected $casts = [
        'vm_count' => 'integer',
        'storage_per_vm_gb' => 'decimal:2',
        'total_storage_gb' => 'decimal:2',
        'price_per_gb' => 'decimal:2',
        'price_backup_software' => 'decimal:2',
        'price_total_monthly' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function backupRetention(): BelongsTo
    {
        return $this->belongsTo(BackupRetentionOption::class, 'backup_retention_id');
    }

    public function backupSoftware(): BelongsTo
    {
        return $this->belongsTo(BackupSoftwareOption::class, 'backup_software_id');
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
