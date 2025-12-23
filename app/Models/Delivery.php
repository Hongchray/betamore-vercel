<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Delivery extends Model
{
    use HasUuids;

    protected $fillable = [
        'delivery_id',
        'name',
        'image',
        'shipping_fee',
        'is_active',
        'description'
    ];

    protected $casts = [
        'shipping_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
