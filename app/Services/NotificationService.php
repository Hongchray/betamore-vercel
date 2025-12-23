<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class NotificationService
{
    private $credentialsPath;
    private $projectId;
    private $fcmV1Url;

    public function __construct()
    {
        $this->credentialsPath = storage_path(config('services.firebase.credentials'));
        $this->projectId = config('services.firebase.project_id');
        $this->fcmV1Url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
    }

    /**
     * Send notification to a single device
     */
    public function sendToDevice($fcmToken, $title, $body, $data = [])
    {
        try {
            // Get OAuth2 access token
            $accessToken = $this->getAccessToken();

            $payload = [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => $data,
                    'webpush' => [
                        'notification' => [
                            'icon' => url('/icon.png'),
                            'badge' => url('/badge.png'),
                        ],
                        'fcm_options' => [
                            'link' => url('/')
                        ]
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->fcmV1Url, $payload);

            $result = $response->json();

            if ($response->successful()) {
                Log::info('FCM notification sent successfully', $result);
                return [
                    'success' => true,
                    'data' => $result
                ];
            } else {
                Log::error('FCM notification failed', [
                    'status' => $response->status(),
                    'response' => $result
                ]);
                return [
                    'success' => false,
                    'error' => $result
                ];
            }
        } catch (\Exception $e) {
            Log::error('FCM notification exception: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    
    private function getAccessToken()
    {
        return Cache::remember('firebase_access_token', 50 * 60, function () {
            return $this->generateAccessToken();
        });
    }

    /**
     * Generate new OAuth2 access token
     */
    private function generateAccessToken()
    {
        // Load service account credentials
        if (!file_exists($this->credentialsPath)) {
            throw new \Exception("Firebase credentials file not found at: {$this->credentialsPath}");
        }

        $credentials = json_decode(file_get_contents($this->credentialsPath), true);
        
        if (!$credentials) {
            throw new \Exception("Invalid Firebase credentials JSON");
        }

        $now = time();
        $exp = $now + 3600; // 1 hour expiration

        // Create JWT header and payload
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT'
        ];

        $payload = [
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $exp,
            'iat' => $now
        ];

        // Base64URL encode header and payload
        $base64UrlHeader = $this->base64UrlEncode(json_encode($header));
        $base64UrlPayload = $this->base64UrlEncode(json_encode($payload));
        
        $signatureInput = $base64UrlHeader . "." . $base64UrlPayload;
        
        // Sign with private key
        $signature = '';
        $success = openssl_sign(
            $signatureInput,
            $signature,
            $credentials['private_key'],
            OPENSSL_ALGO_SHA256
        );

        if (!$success) {
            throw new \Exception("Failed to sign JWT");
        }

        $base64UrlSignature = $this->base64UrlEncode($signature);
        
        // Complete JWT
        $jwt = $signatureInput . "." . $base64UrlSignature;

        // Exchange JWT for access token
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]);

        if (!$response->successful()) {
            throw new \Exception("Failed to get access token: " . $response->body());
        }

        $result = $response->json();
        
        if (!isset($result['access_token'])) {
            throw new \Exception("Access token not found in response");
        }

        return $result['access_token'];
    }

    /**
     * Base64URL encode (different from standard base64)
     */
    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}