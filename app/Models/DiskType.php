<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DiskType extends Model
{
    protected $table = 'disk_types';
    protected $fillable = ['name','slug','price_per_gb','is_active','sort_order'];
    protected $casts = ['is_active'=>'boolean','price_per_gb'=>'decimal:4'];
}
