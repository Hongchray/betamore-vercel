<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PhoneVerification extends Model
{
    protected $fillable = [
        'phone',
        'otp_code',
        'expires_at',
        'is_verified',
        'verification_token',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at < Carbon::now();
    }

    public function isValid(string $otp): bool
    {
        return $this->otp_code === $otp && !$this->isExpired() && !$this->is_verified;
    }
}
