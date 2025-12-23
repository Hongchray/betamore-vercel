<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Address;
use App\Models\Delivery;
use App\Models\Payment;


class Order extends Model
{
    use HasUuids;

    protected $fillable = [
        'order_number',
        'user_id',
        'delivery_id',
        'delivery_fee',
        'total_amount',
        'status',
        'address_id',
        'total_price',
        'note'
    ];
     protected $casts = [
        'delivery_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

     public function orderPayment()
    {
        return $this->hasOne(OrderPayment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }  
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }


}
