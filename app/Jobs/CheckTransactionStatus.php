<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckTransactionStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $tranId;

    public function __construct(string $tranId)
    {
        $this->tranId = $tranId;
    }

    public function handle()
    {
        Log::info('CheckTransactionStatus job started', [
            'tran_id'  => $this->tranId,
            'timestamp' => now()->toDateTimeString(),
        ]);

        // Find the order by tran_id
        $order = Order::whereHas('orderPayment', fn($q) => $q->where('tran_id', $this->tranId))
            ->with('orderPayment')
            ->first();

        if (!$order) {
            Log::warning('Order not found for CheckTransactionStatus job', [
                'tran_id' => $this->tranId,
            ]);
            return;
        }

        $merchantId = config('payway.merchant_id');
        $apiKey = config('payway.api_key');
        $reqTime = date('YmdHis');

        $hashString = $reqTime . $merchantId . $this->tranId;
        $hash = base64_encode(hash_hmac('sha512', $hashString, $apiKey, true));

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(
            'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/check-transaction-2',
            [
                'req_time'    => $reqTime,
                'merchant_id' => $merchantId,
                'tran_id'     => $this->tranId,
                'hash'        => $hash,
            ]
        );

        $data = $response->json();

        Log::info('CheckTransactionStatus response', [
            'tran_id' => $this->tranId,
            'response' => $data,
        ]);

        if (isset($data['data.payment_status']) && $order->orderPayment) {
            $order->orderPayment->update([
                'payment_status' => $data['data.payment_status'],
                'amount' => $data['amount'] ?? $order->orderPayment->amount,
                'tran_id' => $data['tran_id'] ?? $this->tranId,
            ]);

            Log::info('Order payment updated by CheckTransactionStatus job', [
                'order_id' => $order->id,
                'payment_status' => $data['data.payment_status'],
            ]);
        }
    }
}
