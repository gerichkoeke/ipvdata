<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class BandwidthOption extends Model
{
    protected $table = 'bandwidth_options';
    protected $fillable = ['name','mbps','price','is_active','sort_order'];
    protected $casts = ['is_active'=>'boolean','price'=>'decimal:2'];
}
