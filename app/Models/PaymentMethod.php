<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PaymentMethod extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'type',
        'description',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function orderPayments()
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function banks()
    {
        return $this->hasMany(Bank::class);
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    // Get active banks for this payment method
    public function getActiveBanks()
    {
        return $this->banks()->where('is_active', true)->get();
    }

    // Get active cards for this payment method
    public function getActiveCards()
    {
        return $this->cards()->where('is_active', true)->get();
    }
}
