<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ABAController extends Controller
{

    public function purchase()
    {
        $req_time = now()->utc()->format('YmdHis');
        $merchant_id = config('aba.merchant_id');
        $tran_id = $req_time;
        $firstname = 'Song';
        $lastname = 'Hongchray';
        $email = 'songhongchray@gmail.com';
        $phone = '0976168988';
        $type = 'pre-auth';
        $payment_option = 'abapay_khqr';
        $item = base64_encode(json_encode([
            ["name" => "product 1","quantity" => 1,"price" => 1.00], 
            ["name" => "product 2","quantity" => 2, "price" => 4.00]
        ]));
        $shipping = 0.1;
        $amount = 0.5;
        $currency = 'USD';
        $return_url =  '';
        $cancel_url = '';
        $skip_success_page = 0;
        $continue_success_url = '';
        $return_deeplink =base64_encode(json_encode([
            "ios_scheme" => "DEEPLINK TO RETURN TO YOUR IOS APP",
            "android_scheme" => "DEEPLINK TO RETURN TO YOUR ANDROID APP"
        ]));
        $custom_fields = '';
        $return_params = '';
        $view_type = 'popup';
        $payment_gate = 0;
        $payout = base64_encode(json_encode([
            ["account" => "004351097","amount"=> 0.5], 
        ]));
        
        $additional_params = '';
        $lifetime = 3600;
        
        $google_pay_token =  '';

        $api_key = config('aba.api_key');
        
        $b4hash = $req_time . $merchant_id . $tran_id . $amount . $item . $shipping . $firstname . $lastname . $email . $phone . $type . $payment_option . $return_url . $cancel_url . $continue_success_url . $return_deeplink . $currency . $custom_fields . $return_params . $payout . $lifetime . $additional_params . $google_pay_token .$skip_success_page;

        $hash = base64_encode(hash_hmac('sha512', $b4hash, $api_key, true));
        $postData = [
            'req_time' => $req_time,
            'merchant_id' => $merchant_id,
            'tran_id' => $tran_id,
            'amount' => $amount,
            'items' => $item,
            'shipping' => $shipping,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'phone' => $phone,
            'type' => $type,
            'payment_option' => $payment_option,
            'return_url' => $return_url,
            'cancel_url' => $cancel_url,
            'continue_success_url' => $continue_success_url,
            'return_deeplink' => $return_deeplink,
            'currency' => $currency,
            'custom_fields' => $custom_fields,
            'return_params' => $return_params,
            'payout' => $payout,
            'lifetime' => $lifetime,
            'additional_params' => $additional_params,
            'google_pay_token' => $google_pay_token,
            'skip_success_page' => $skip_success_page,
            'hash' => $hash,
        ];

        try {
            $multipartData = collect($postData)->map(function ($value, $key) {
                return [
                    'name' => $key,
                    'contents' => (string) $value
                ];
            })->values()->toArray();
            $response = Http::asMultipart()->post(
                'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/purchase',
                $multipartData
            );

                       

            if ($response->successful()) {
                return response($response)
                    ->header('Content-Type', 'multipart/form-data');
            }


            return response()->json([
                'error' => 'Payment gateway error',
                'status' => $response->status(),
                'message' => $response->body()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Purchase failed: ' . $e->getMessage()
            ], 500);
        }
    }

}
