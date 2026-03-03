<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BackupSoftwareOption extends Model
{
    protected $table    = 'backup_software_options';
    protected $fillable = [
        'name', 'slug', 'edition', 'license_model',
        'price_per_unit', 'included_units',
        'has_agent', 'price_per_agent',
        'is_active', 'sort_order', 'notes',
    ];
    protected $casts = [
        'is_active'  => 'boolean',
        'has_agent'  => 'boolean',
        'price_per_unit'  => 'decimal:2',
        'price_per_agent' => 'decimal:2',
    ];

    public function getLicenseModelLabelAttribute(): string
    {
        return match($this->license_model) {
            'per_vm'     => 'Por VM',
            'per_socket' => 'Por Socket',
            'per_tb'     => 'Por TB',
            default      => $this->license_model,
        };
    }

    /**
     * Calcula o custo mensal baseado na quantidade de VMs
     */
    public function calculateCost(int $vmCount): float
    {
        if ($this->price_per_unit == 0) return 0;

        return match($this->license_model) {
            'per_vm' => (float) ($this->price_per_unit * $vmCount),
            default  => (float) $this->price_per_unit,
        };
    }
}
