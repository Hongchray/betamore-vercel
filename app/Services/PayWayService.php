<?php

namespace App\Services;

class PayWayService
{
    public function getHash(string $str): string
    {
        return base64_encode(
            hash_hmac('sha512', $str, config('payway.api_key'), true)
        );
    }

    public function getApiUrl(): string
    {
        return config('payway.api_url');
    }
}
