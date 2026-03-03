<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectVm extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'cpu_cores',
        'ram_gb',
        'disk_os_gb',
        'disk_os_type_id',
        'os_distribution_id',
        'remote_desktop_type_id',
        'rds_license_mode_id',
        'rds_license_qty',
        'endpoint_security_id',
        'has_backup',
        'backup_retention_id',
        'backup_software_id',
        'backup_storage_gb',
        'price_backup_software',
        'price_cpu',
        'price_ram',
        'price_disk_os',
        'price_os_license',
        'price_rds',
        'price_endpoint',
        'price_backup',
        'price_total_monthly',
        'status',
        'discount_amount',
    ];

    protected $casts = [
        'cpu_cores'        => 'integer',
        'ram_gb'           => 'integer',
        'disk_os_gb'       => 'integer',
        'rds_license_qty'  => 'integer',
        'backup_storage_gb'=> 'integer',
        'has_backup'       => 'boolean',
        'discount_amount'  => 'decimal:2',
    ];

    // ── Relacionamentos ──────────────────────────────────────────

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function osDistribution(): BelongsTo
    {
        return $this->belongsTo(OsDistribution::class);
    }

    /** Tipo do disco do SO */
    public function diskOsType(): BelongsTo
    {
        return $this->belongsTo(DiskType::class, 'disk_os_type_id');
    }

    public function remoteDesktopType(): BelongsTo
    {
        return $this->belongsTo(RemoteDesktopType::class, 'remote_desktop_type_id');
    }

    public function rdsLicenseMode(): BelongsTo
    {
        return $this->belongsTo(RdsLicenseMode::class);
    }

    /** Endpoint Security — chave correta do banco */
    public function endpointSecurity(): BelongsTo
    {
        return $this->belongsTo(EndpointSecurityOption::class, 'endpoint_security_id');
    }

    public function backupRetention(): BelongsTo
    {
        return $this->belongsTo(BackupRetentionOption::class, 'backup_retention_id');
    }

    public function backupSoftware(): BelongsTo
    {
        return $this->belongsTo(BackupSoftwareOption::class, 'backup_software_id');
    }

    /** Discos adicionais */
    public function additionalDisks(): HasMany
    {
        return $this->hasMany(ProjectVmDisk::class, 'project_vm_id')->orderBy('sort_order');
    }

    /** Alias para manter compatibilidade com código antigo */
    public function disks(): HasMany
    {
        return $this->additionalDisks();
    }

    // ── Cálculo de custo ─────────────────────────────────────────

    public function getTotalMonthlyCost(): float
    {
        return round((float) ($this->price_total_monthly ?? 0), 2);
    }

    public function getTotalMonthlyCostWithDiscount(): float
    {
        return max(0, $this->getTotalMonthlyCost() - (float) ($this->discount_amount ?? 0));
    }
}
