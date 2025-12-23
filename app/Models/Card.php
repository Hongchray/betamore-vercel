<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Card extends Model
{
    use HasUuids;

    protected $fillable = [
        'payment_method_id',
        'name',
        'code',
        'merchant_account_name',
        'merchant_account_number',
        'description',
        'logo',
        'additional_info',
        'is_active',
    ];

    protected $casts = [
        'additional_info' => 'array',
        'is_active' => 'boolean',
    ];

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function orderPayments()
    {
        return $this->hasMany(OrderPayment::class);
    }
}
