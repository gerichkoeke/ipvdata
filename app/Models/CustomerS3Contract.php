<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerS3Contract extends Model
{
    protected $fillable = [
        'customer_id',
        'size_gb',
        'price_per_gb',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
