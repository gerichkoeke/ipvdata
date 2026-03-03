<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MsLicenseSku extends Model
{
    protected $table    = 'ms_license_skus';
    protected $fillable = [
        'os_distribution_id','name','part_number','license_type',
        'cores_per_license','pack_size','threshold_cores',
        'billing_period','is_cal','cal_type',
        'sa_available','cost_price','sale_price','is_active','notes',
    ];
    protected $casts = [
        'sa_available' => 'boolean',
        'is_cal'       => 'boolean',
        'is_active'    => 'boolean',
        'cost_price'   => 'decimal:2',
        'sale_price'   => 'decimal:2',
    ];

    public function osDistribution()  { return $this->belongsTo(OsDistribution::class); }
    public function pools()           { return $this->hasMany(MsLicensePool::class, 'sku_id'); }
    public function customerLicenses(){ return $this->hasMany(MsCustomerLicense::class, 'sku_id'); }

    /** Disponível em pools ativos */
    public function getTotalAvailableCoresAttribute(): int
    {
        return $this->pools()
            ->where('status', 'active')
            ->get()
            ->sum(fn($p) => $p->available_cores);
    }

    /** Label para billing_period */
    public function getBillingLabelAttribute(): string
    {
        return match($this->billing_period) {
            '1year'   => '1 Ano',
            '3year'   => '3 Anos',
            'monthly' => 'Mensal',
            default   => $this->billing_period,
        };
    }
}
