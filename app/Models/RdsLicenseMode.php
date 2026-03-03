<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class RdsLicenseMode extends Model
{
    protected $table = 'rds_license_modes';
    protected $fillable = ['remote_desktop_type_id','name','slug','price_per_unit','is_active'];
    protected $casts = ['is_active'=>'boolean','price_per_unit'=>'decimal:2'];
    public function remoteDesktopType() { return $this->belongsTo(RemoteDesktopType::class); }
}
