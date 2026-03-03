<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NetworkType extends Model
{
    protected $table    = 'network_types';
    protected $fillable = [
        'name', 'slug', 'has_public_ip', 'default_ips', 'price',
        'requires_firewall', 'is_active', 'sort_order',
    ];
    protected $casts = [
        'has_public_ip'    => 'boolean',
        'requires_firewall'=> 'boolean',
        'is_active'        => 'boolean',
        'price'            => 'decimal:2',
    ];

    public function isLanToLan(): bool
    {
        return $this->slug === 'lan-to-lan';
    }
}
