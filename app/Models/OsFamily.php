<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OsFamily extends Model
{
    protected $table    = 'os_families';
    protected $fillable = ['name', 'slug', 'is_active', 'sort_order'];
    protected $casts    = ['is_active' => 'boolean'];
    public function distributions() {
        return $this->hasMany(OsDistribution::class, 'os_family_id');
    }
}
