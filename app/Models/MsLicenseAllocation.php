<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MsLicenseAllocation extends Model
{
    protected $table    = 'ms_license_allocations';
    protected $fillable = [
        'pool_id','customer_id','project_vm_id',
        'allocated_cores','allocated_at','released_at','status','notes',
    ];
    protected $casts = [
        'allocated_at' => 'date',
        'released_at'  => 'date',
    ];

    public function pool()      { return $this->belongsTo(MsLicensePool::class, 'pool_id'); }
    public function customer()  { return $this->belongsTo(Customer::class); }
    public function projectVm() { return $this->belongsTo(ProjectVm::class); }
}
