<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\ItemModification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\IdGeneratorService;
use Illuminate\Validation\ValidationException;
use App\Models\Delivery;
use Carbon\Carbon;
use App\Enums\OrderStatus;
use App\Services\TelegramService;
use App\Mail\InvoiceEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Http;
use App\Enums\PaymentStatus;
use App\Jobs\CheckTransactionStatus;

class OrderController extends Controller
{
    protected IdGeneratorService $idGenerator;

    public function __construct(IdGeneratorService $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    public function index()
    {
        $user = Auth::user(); 
        $orders = $user->orders()->with('items')->latest()->get();
        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully.',
            'data' => $orders,
        ]);
    }
    
    public function addToCart(Request $request)
    {
        $request->validate([
            'item_modification_id' => 'required|exists:item_modifications,id',
            'quantity' => 'required|integer|min:1',
            'delivery_id' => 'nullable|exists:deliveries,id',
            'address_id' => 'nullable|exists:addresses,id',
            'note' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();

            $itemModification = ItemModification::with(['item.firstImage'])->findOrFail($request->item_modification_id);
            
            $cartOrder = Order::where('user_id', $user->id)
                ->where('status', OrderStatus::IN_CART)
                ->first();

                $orderId = $this->idGenerator->generateId('order', 'orders', 'order_number', 10);

           
            if (!$cartOrder) {
                $deliveryFee = 0.00;
                if ($request->delivery_id) {
                    $delivery = Delivery::findOrFail($request->delivery_id);
                    $deliveryFee = $delivery->shipping_fee;
                }

                $cartOrder = Order::create([
                    'order_number' => $orderId,
                    'user_id' => $user->id,
                    'delivery_id' => $request->delivery_id,
                    'delivery_fee' => $deliveryFee,
                    'total_amount' => 0.00,
                    'total_price' => 0.00,
                    'status' => OrderStatus::IN_CART,
                    'address_id' => $request->address_id,
                    'note' => $request->note
                ]);

                OrderPayment::create([
                    'order_id' => $cartOrder->id,
                    'payment_method_id' => null,
                    'payment_status' => PaymentStatus::Pending,
                    'amount' => 0.00
                ]);
            }

            $existingItem = OrderItem::where('order_id', $cartOrder->id)
                ->where('item_modification_id', $request->item_modification_id)
                ->first();

            if ($existingItem) {
                $finalPrice = $this->calculateFinalPrice($request->item_modification_id);
                
                $existingItem->quantity += $request->quantity;
                $existingItem->unit_price = $finalPrice;
                $existingItem->total_price = $existingItem->quantity * $finalPrice;
                $existingItem->save();

                $orderItem = $existingItem;
            } else {
                $itemName = $itemModification->item->name_en ?? 'Unknown Item';
                $itemImage = $itemModification->item->firstImage->image ?? null;
                
                $finalPrice = $this->calculateFinalPrice($request->item_modification_id);
                
                $orderItem = OrderItem::create([
                    'order_id' => $cartOrder->id,
                    'item_modification_id' => $request->item_modification_id,
                    'name' => $itemName . ' (' . $itemModification->modification_name . ')',
                    'image' => $itemImage,
                    'quantity' => $request->quantity,
                    'unit_price' => $finalPrice,
                    'total_price' => $request->quantity * $finalPrice,
                    'notes' => $request->note
                ]);
            }

            $this->recalculateOrderTotals($cartOrder);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart successfully',
                'data' => [
                    'order_id' => $cartOrder->order_number,
                    'cart_items_count' => $cartOrder->orderItems()->count(),
                    'order_item_id' => $orderItem->id
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item to cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCart()
    {
        $user = auth()->user(); 

        $orders = $user->orders()
            ->where('status', OrderStatus::IN_CART)
            ->with('items.modification')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'In-cart orders retrieved successfully.',
            'data' => $orders,
        ]);
    }
    
    public function updateCartItem(Request $request, $orderItemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            
            $orderItem = OrderItem::whereHas('order', function($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', OrderStatus::IN_CART);
            })->findOrFail($orderItemId);

            $orderItem->update([
                'quantity' => $request->quantity,
                'total_price' => $request->quantity * $orderItem->unit_price
            ]);

            $this->recalculateOrderTotals($orderItem->order);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cart item updated successfully',
                'data' => [
                    'order_item_id' => $orderItem->id,
                    'new_quantity' => $orderItem->quantity,
                    'new_total_price' => $orderItem->total_price,
                    'cart_total' => $orderItem->order->total_amount
                ]
            ], 200);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found',
            ], 404);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart item',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function removeCartItem($orderItemId)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            $inCartOrder = $user->orders()->where('status', OrderStatus::IN_CART)->first();

            if (!$inCartOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active cart found for user.',
                ], 404);
            }

            $orderItem = OrderItem::whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', OrderStatus::IN_CART);
            })->findOrFail($orderItemId);

            $order = $orderItem->order;

            $orderItem->delete();

            $remainingItems = $order->orderItems()->count();

            if ($remainingItems > 0) {
                $this->recalculateOrderTotals($order);
            } else {
                $order->update([
                    'total_price' => 0.00,
                    'total_amount' => $order->delivery_fee
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart successfully',
                'data' => [
                    'removed_item_id' => $orderItemId,
                    'remaining_items_count' => $remainingItems,
                    'cart_total' => $order->total_amount
                ]
            ], 200);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found.',
            ], 404);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item from cart.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function makeOrder(Request $request, TelegramService $telegram)
    {
        $request->validate([
            'delivery_id' => 'required|exists:deliveries,id',
            'address_id' => 'required|exists:addresses,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'note' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            
            $cartOrder = Order::where('user_id', $user->id)
                ->where('status', OrderStatus::IN_CART)
                ->with(['orderItems', 'orderPayment'])
                ->first();

            if (!$cartOrder) {
                throw ValidationException::withMessages([
                    'cart' => ['No items in cart found']
                ]);
            }

            if ($cartOrder->orderItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => ['Cart is empty']
                ]);
            }

            $delivery = Delivery::findOrFail($request->delivery_id);
            $deliveryFee = $delivery->shipping_fee;
            $cartOrder->update([
                'delivery_id' => $request->delivery_id,
                'delivery_fee' => $deliveryFee,
                'address_id' => $request->address_id,
                'status' => OrderStatus::PENDING,
                'note' => $request->note,
                'payment_method_id' => $request->payment_method_id,
            ]);

            $this->recalculateOrderTotals($cartOrder);

            if ($cartOrder->orderPayment) {
                $cartOrder->orderPayment->update([
                    'payment_method_id' => $request->payment_method_id,
                    'payment_status' => PaymentStatus::Pending,
                    'tran_id' => $cartOrder->order_number,
                    'amount' => $cartOrder->total_amount
                ]);
            }

            DB::commit();

            
            if ($user->email) {
                try {
                    Mail::to($user->email)->send(new InvoiceEmail($cartOrder));
                } catch (\Throwable $mailError) {
                    Log::error('Failed to send invoice email: ' . $mailError->getMessage());
                }
            }

           


            $paywayResponse = null;

            $paymentMethod = DB::table('payment_methods')
                ->where('id', $request->payment_method_id)
                ->first();

            if ($paymentMethod && $paymentMethod->type !== 'cash_on_delivery') {
                $paywayResponse = $this->initiatePayWayCheckout($cartOrder, $user, $request->payment_method_id);
            }

            $link = config('app.url') . "/orders/{$cartOrder->id}";

            $telegram->sendMessage(
                "*New Order Received!*\n\n" .
                "👤 *Customer:* {$user->first_name} {$user->last_name}\n" .
                "📦 *Order Number:* `{$cartOrder->order_number}`\n" .
                "🛒 *Items:* {$cartOrder->orderItems->count()}\n" .
                "💰 *Total:* {$cartOrder->total_amount}$\n" .
                "🚚 *Delivery:* {$delivery->name} ({$deliveryFee}$)\n" .
                "💳 *Payment Method:* {$paymentMethod->name}\n" .
                "🔗 [View Order]({$link})"

            );

            CheckTransactionStatus::dispatch($cartOrder->orderPayment->tran_id);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'order_id' => $cartOrder->id,
                    'order_number' => $cartOrder->order_number,
                    'total_amount' => $cartOrder->total_amount,
                    'status' => $cartOrder->status,
                    'payway' => $paywayResponse
                ]
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function initiatePayWayCheckout($order, $user, $paymentMethodId)
    {
        $merchantId = config('payway.merchant_id');
        $apiKey     = config('payway.api_key');

        $tranId   = $order->order_number;
        $reqTime  = date('YmdHis');
        $amount   = $order->total_amount;
        $firstname = $user->first_name ?? 'Customer';
        $lastname  = $user->last_name ?? '';
        $phone     = $user->phone ?? '012345678';
        $email     = $user->email ?? 'noemail@example.com';
        $returnParams = 'OrderID:' . $order->id;

        $hashString = $reqTime . $merchantId . $tranId . $amount . $firstname . $lastname . $email . $phone . $returnParams;
        $hash       = base64_encode(hash_hmac('sha512', $hashString, $apiKey, true));

        $order->orderPayment()->updateOrCreate(
            ['order_id' => $order->id],
            [
                'payment_method_id' => $paymentMethodId,                
                'tran_id'           => $tranId,
                'amount'            => $amount,
                'payment_status'    => PaymentStatus::Pending
            ]
        );
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

        return $response->json();
    }

    public function checkStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'nullable|uuid',
            'tran_id'  => 'nullable|string',
        ]);

        if ($request->filled('order_id')) {
            $order = Order::with('orderPayment')->findOrFail($request->order_id);
            $tranId = $order->orderPayment->tran_id ?? $order->order_number;
        } elseif ($request->filled('tran_id')) {
            $tranId = $request->tran_id;
            $order = Order::whereHas('orderPayment', fn($q) => $q->where('tran_id', $tranId))
                ->with('orderPayment')
                ->firstOrFail();
        } else {
            return response()->json(['error' => 'order_id or tran_id is required'], 422);
        }

        $merchantId = config('payway.merchant_id');
        $apiKey     = config('payway.api_key');
        $reqTime    = date('YmdHis');

        $hashString = $reqTime . $merchantId . $tranId;
        $hash = base64_encode(hash_hmac('sha512', $hashString, $apiKey, true));

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

        $data = $response->json();

        $paywayTranId = $data['tran_id'] ?? $tranId;

        // Removed database update — only check status
        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'tran_id'  => $paywayTranId,
            'payway'   => $data,
        ]);
    }

    private function recalculateOrderTotals(Order $order)
    {
        $subtotal = $order->orderItems()->sum(DB::raw('quantity * unit_price'));
        $totalAmount = $subtotal + $order->delivery_fee;
        $order->update([
            'total_price' => $subtotal,
            'total_amount' => $totalAmount
        ]);
    }

    private function calculateFinalPrice($itemModificationId)
    {
        $now = Carbon::now();
        
        $itemModification = ItemModification::with(['item.itemPromotions.promotion'])
            ->findOrFail($itemModificationId);
        
        $unitPrice = floatval($itemModification->unit_price);
        
        $activePromotion = collect($itemModification->item->itemPromotions)
            ->pluck('promotion')
            ->first(function ($promotion) use ($now) {
                if (!$promotion) return false;
                
                return $now->toDateString() >= $promotion->start_date->toDateString() &&
                    $now->toDateString() <= $promotion->end_date->toDateString();
            });
        
        if ($activePromotion) {
            $discountAmount = 0;
            
            if ($activePromotion->type === 'percent') {
                $discountAmount = $unitPrice * floatval($activePromotion->discount_value) / 100;
            } elseif (in_array($activePromotion->type, ['flat', 'amount'])) {
                $discountAmount = floatval($activePromotion->discount_value);
            }
            
            $finalPrice = max(0, $unitPrice - $discountAmount);
            return round($finalPrice, 2);
        }
        
        return $unitPrice;
    }
}