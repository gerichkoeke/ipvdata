<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ResourcePricing extends Model
{
    protected $table    = 'resource_pricing'; // evita pluralização para resource_pricings
    protected $fillable = ['resource_type','name','price','unit','is_active'];
    protected $casts    = ['is_active'=>'boolean','price'=>'decimal:4'];
}
