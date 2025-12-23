<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class OrderPayment extends Model
{
    use HasUuids;

    protected $fillable = [
        'order_id',
        'payment_method_id',
        'payment_status',
        'amount',
        'paid_at',
        'bank_id',
        'card_id',
        'payment_details',
        'notes',
        'tran_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
