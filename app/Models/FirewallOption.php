<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FirewallOption extends Model
{
    protected $table = 'firewall_options';
    protected $fillable = ['name','slug','price','is_active','sort_order'];
    protected $casts = ['is_active'=>'boolean','price'=>'decimal:2'];
}
