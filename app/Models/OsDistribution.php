<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OsDistribution extends Model
{
    protected $table    = 'os_distributions';
    protected $fillable = [
        'os_family_id', 'name', 'version', 'price',
        'requires_license', 'license_per_core', 'min_cores',
        'is_active', 'sort_order',
    ];
    protected $casts = [
        'requires_license' => 'boolean',
        'license_per_core' => 'boolean',
        'is_active'        => 'boolean',
        'price'            => 'decimal:2',
    ];

    public function family()
    {
        return $this->belongsTo(OsFamily::class, 'os_family_id');
    }

    /**
     * Calcula o custo de licença baseado nos cores da VM
     */
    public function calculateLicenseCost(int $cpuCores): float
    {
        if (!$this->requires_license || $this->price == 0) {
            return 0;
        }

        if ($this->license_per_core) {
            // Windows Server: mínimo de min_cores cores
            $billableCores = max($cpuCores, $this->min_cores);
            return (float) ($this->price * $billableCores);
        }

        return (float) $this->price;
    }
}
