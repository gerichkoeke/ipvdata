<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MsCustomerLicense extends Model
{
    use SoftDeletes;
    protected $table    = 'ms_customer_licenses';
    protected $fillable = [
        'customer_id','sku_id','project_vm_id','cores','license_modality',
        'part_number_purchased','invoice_number',
        'tenant_id','tenant_name','ms_customer_id',
        'sa_years','purchased_at','sa_expires_at',
        'cost_per_core','total_cost','status','notes',
    ];
    protected $casts = [
        'purchased_at'  => 'date',
        'sa_expires_at' => 'date',
        'cost_per_core' => 'decimal:4',
        'total_cost'    => 'decimal:2',
    ];

    public function customer()     { return $this->belongsTo(Customer::class); }
    public function sku()          { return $this->belongsTo(MsLicenseSku::class, 'sku_id'); }
    public function projectVm()    { return $this->belongsTo(ProjectVm::class); }

    /** Calcula total_cost ao salvar */
    protected static function booted(): void
    {
        static::saving(function (self $model) {
            $model->total_cost = $model->cores * $model->cost_per_core;
        });
    }
}
