<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PayWayController extends Controller
{
    public function showCheckout()
    {
        $merchantId = config('payway.merchant_id');
        $apiKey = config('payway.api_key');

        $tranId = '12345';
        $reqTime = time();
        $amount = '120';
        $firstname = 'Hongchray';
        $lastname = 'Song';
        $phone = '0976168988';
        $email = 'arucanknight@gmail.com';
        $returnParams = 'Hello World!';

        $hashString = $reqTime . $merchantId . $tranId . $amount . $firstname . $lastname . $email . $phone . $returnParams;
        $hash = base64_encode(hash_hmac('sha512', $hashString, $apiKey, true));

        $response = Http::asMultipart()->post(
            'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/purchase',
            [
                'merchant_id'   => $merchantId,
                'tran_id'       => $tranId,
                'amount'        => $amount,
                'firstname'     => $firstname,
                'lastname'      => $lastname,
                'phone'         => $phone,
                'email'         => $email,
                'return_params' => $returnParams,
                'hash'          => $hash,
                'req_time'      => $reqTime
            ]
        );
        return response()->json($response->json(), $response->status());
    }

    public function checkStatus(Request $request)
    {
        $merchantId = config('payway.merchant_id');
        $apiKey     = config('payway.api_key');

        $tranId  = $request->input('tran_id'); 
        $reqTime = date('YmdHis');

        $hashString = $reqTime . $merchantId . $tranId;
        $hash       = base64_encode(hash_hmac('sha512', $hashString, $apiKey, true));

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(
            'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/check-transaction-2',
            [
                'req_time'    => $reqTime,
                'merchant_id' => $merchantId,
                'tran_id'     => $tranId,
                'hash'        => $hash,
            ]
        );

        return response()->json($response->json(), $response->status());
    }
}
