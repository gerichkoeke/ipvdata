<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'description',
        'type',
        'billing_cycle',
        'price',
        'cost',
        'is_active',
        'features',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'features'  => 'array',
        'price'     => 'decimal:2',
        'cost'      => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_products')
            ->withPivot(['quantity', 'unit_price'])
            ->withTimestamps();
    }
}
