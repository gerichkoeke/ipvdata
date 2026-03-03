<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class EndpointSecurityOption extends Model
{
    protected $table = 'endpoint_security_options';
    protected $fillable = ['name','slug','price_per_vm','is_active','sort_order'];
    protected $casts = ['is_active'=>'boolean','price_per_vm'=>'decimal:2'];
}
