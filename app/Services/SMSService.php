<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SMSService
{
    private string $apiUrl;
    private string $privateKey;
    private string $xSecret;
    private string $sender;

    public function __construct()
    {
        $this->apiUrl = config('sms.api_url');
        $this->privateKey = config('sms.private_key');
        $this->xSecret = config('sms.x_secret');
        $this->sender = config('sms.sender', 'PlasGateUAT');
    }

    public function sendSms(string $phone, string $content): array
    {
        try {
            $response = Http::withHeaders([
                'X-Secret' => $this->xSecret,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '?private_key=' . $this->privateKey, [
                'sender' => $this->sender,
                'to' => $phone,
                'content' => $content,
            ]);

            if ($response->failed()) {
                Log::error('SMS sending failed', [
                    'error' => $response->body(),
                    'phone' => $phone
                ]);
                throw new Exception('SMS sending failed: ' . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Failed to send SMS', [
                'error' => $e->getMessage(),
                'phone' => $phone
            ]);
            throw new Exception('Failed to send SMS: ' . $e->getMessage());
        }
    }
    /**
     * Format withdrawal code message
     *
     * @param string $withdrawalCode
     * @return string
     */
    public function formatOTPResetPasswordMessage(string $code): string
    {
        return sprintf(
            "Your OTP code is %s.",
            $code
        );
    }
    public function formatOTPRegistrationMessage(string $code): string
    {
        return sprintf(
            "Your OTP code is %s.",
            $code
        );
    }
}
