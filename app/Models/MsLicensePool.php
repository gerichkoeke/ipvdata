<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MsLicensePool extends Model
{
    use SoftDeletes;
    protected $table    = 'ms_license_pools';
    protected $fillable = [
        'sku_id','invoice_number','purchased_cores','cost_per_core',
        'sa_years','purchased_at','sa_expires_at','status','notes',
    ];
    protected $casts = [
        'purchased_at'  => 'date',
        'sa_expires_at' => 'date',
        'cost_per_core' => 'decimal:4',
    ];

    public function sku()         { return $this->belongsTo(MsLicenseSku::class, 'sku_id'); }
    public function allocations() { return $this->hasMany(MsLicenseAllocation::class, 'pool_id'); }

    /** Cores já alocados */
    public function getAllocatedCoresAttribute(): int
    {
        return $this->allocations()->where('status', 'active')->sum('allocated_cores');
    }

    /** Cores disponíveis */
    public function getAvailableCoresAttribute(): int
    {
        return $this->purchased_cores - $this->allocated_cores;
    }
}
