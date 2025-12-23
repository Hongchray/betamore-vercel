<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class OrderItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'order_id',
        'item_modification_id',
        'name',
        'image',
        'quantity',
        'unit_price',
        'total_price',
        'notes',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function modification()
    {
        return $this->belongsTo(ItemModification::class, 'item_modification_id');
    }
}
