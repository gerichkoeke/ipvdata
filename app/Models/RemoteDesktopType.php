<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class RemoteDesktopType extends Model
{
    protected $table = 'remote_desktop_types';
    protected $fillable = ['name','slug','has_license_modes','is_active','sort_order'];
    protected $casts = ['has_license_modes'=>'boolean','is_active'=>'boolean'];
    public function licenseModes() { return $this->hasMany(RdsLicenseMode::class); }
}
