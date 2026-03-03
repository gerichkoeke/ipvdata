<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'partner_id',
        'name',
        'description',
        'status',
        'currency',
        'network_type_id',
        'bandwidth_option_id',
        'network_topology',
        'has_firewall',
        'firewall_option_id',
        'lan_type_id',
        'partner_commission_percentage',
        'global_discount_amount',
        'global_discount_currency',
        'network_discount_amount',
    ];

    protected $casts = [
        'has_firewall' => 'boolean',
        'partner_commission_percentage' => 'decimal:2',
        'global_discount_amount' => 'decimal:2',
        'network_discount_amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function vms(): HasMany
    {
        return $this->hasMany(ProjectVm::class);
    }

    public function s3Storage(): HasMany
    {
        return $this->hasMany(ProjectS3Storage::class);
    }

    public function backupStandalone(): HasMany
    {
        return $this->hasMany(ProjectBackupStandalone::class);
    }

    public function networkType(): BelongsTo
    {
        return $this->belongsTo(NetworkType::class);
    }

    public function bandwidthOption(): BelongsTo
    {
        return $this->belongsTo(BandwidthOption::class);
    }

    public function firewallOption(): BelongsTo
    {
        return $this->belongsTo(FirewallOption::class);
    }

    public function lanType(): BelongsTo
    {
        return $this->belongsTo(NetworkType::class, 'lan_type_id');
    }

    /**
     * Calcula o custo total mensal do projeto incluindo descontos
     */
    public function getTotalMonthlyCost(): float
    {
        $total = 0;

        // VMs e seus componentes
        foreach ($this->vms as $vm) {
            $total += $vm->getTotalMonthlyCostWithDiscount();
        }

        // S3 Storage
        foreach ($this->s3Storage as $s3) {
            $total += $s3->getTotalMonthlyCostWithDiscount();
        }

        // Backup Standalone
        foreach ($this->backupStandalone as $backup) {
            $total += $backup->getTotalMonthlyCostWithDiscount();
        }

        // Rede
        $networkCost = $this->getNetworkCost();
        $total += max(0, $networkCost - $this->network_discount_amount);

        // Desconto global
        $total = max(0, $total - $this->global_discount_amount);

        return round($total, 2);
    }

    /**
     * Calcula o custo da rede
     */
    public function getNetworkCost(): float
    {
        $cost = 0;

        if ($this->networkType) {
            $cost += $this->networkType->price ?? 0;
        }

        if ($this->bandwidthOption) {
            $cost += $this->bandwidthOption->monthly_cost ?? 0;
        }

        if ($this->has_firewall && $this->firewallOption) {
            $cost += $this->firewallOption->monthly_cost ?? 0;
        }

        if ($this->lanType) {
            $cost += $this->lanType->price ?? 0;
        }

        return round($cost, 2);
    }

    /**
     * Verifica se o parceiro tem comissão variável
     */
    public function hasVariableCommission(): bool
    {
        return $this->partner && $this->partner->commission_type === 'variable';
    }
}
