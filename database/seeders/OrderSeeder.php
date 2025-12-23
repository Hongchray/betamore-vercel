<?php

namespace Database\Seeders;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\User;
use App\Models\Address;
use App\Models\Delivery;
use App\Models\ItemModification;
use App\Models\PaymentMethod;
use App\Models\Bank;
use App\Models\Card;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as FakerFactory;

class OrderSeeder extends Seeder
{
    public function run(): void
{
    $faker = FakerFactory::create();

    $users = User::all();
    $itemModifications = ItemModification::with('item')->get();
    $deliveries = Delivery::all();
    $addresses = Address::all();
    $paymentMethods = PaymentMethod::all();

    $number = 1;

    for ($i = 0; $i < 10; $i++) {
        $user = $users->random();
        $delivery = $deliveries->random();
        $address = $addresses->random();
        $paymentMethod = $paymentMethods->random();

        $orderId = Str::uuid();
        $deliveryFee = $delivery->shipping_fee;
        $orderNumber = 'ORD' . str_pad($number + $i, 10, '0', STR_PAD_LEFT);

        $orderStatus = 'pending';
        $deliveryStatus = 'pending';

        $note = $faker->optional(0.3)->sentence(); // ✅ fixed
        $order = Order::create([
            'id' => $orderId,
            'order_number' => $orderNumber,
            'user_id' => $user->id,
            'delivery_id' => $delivery->id,
            'delivery_fee' => $deliveryFee,
            'total_amount' => 0,
            'status' => $orderStatus,
            'payment_method_id' => $paymentMethod->id,
            'address_id' => $address->id,
            'total_price' => 0,
            'note' => $note,
        ]);

        $total = 0;

        for ($j = 0; $j < rand(1, 5); $j++) {
            $mod = $itemModifications->random();
            $qty = rand(1, 3);
            $unitPrice = $mod->unit_price;
            $totalPrice = $qty * $unitPrice;

            OrderItem::create([
                'id' => Str::uuid(),
                'order_id' => $order->id,
                'item_modification_id' => $mod->id,
                'name' => $mod->item->name_en ?? $mod->item->name_km,
                'image' => $mod->item->images->random()?->image ?? $mod->item->image ?? null,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'notes' => $faker->optional()->sentence(), // ✅ fixed
            ]);

            $total += $totalPrice;
        }

        $order->update([
            'total_price' => $total,
            'total_amount' => $total + $deliveryFee,
        ]);

        $this->createOrderPayment($order, $paymentMethod, $faker); // pass faker
    }
}

private function createOrderPayment($order, $paymentMethod, $faker)
{
    $customerAccountName = $faker->name();
    $customerAccountNumber = null;
    $transactionReference = null;
    $notes = null;

    switch ($paymentMethod->type) {
        case 'bank_transfer':
            $customerAccountNumber = $faker->numerify('###-###-######-##');
            $transactionReference = 'TXN' . $faker->numerify('##########');
            $notes = "Bank transfer from ABA Payway";
            break;

        case 'card':
            $customerAccountNumber = '**** **** **** ' . $faker->numerify('####');
            $transactionReference = 'CARD' . $faker->numerify('############');
            $notes = "Card payment via CARD";
            break;

        case 'cash':
            $notes = "Cash on delivery payment";
            break;

        default:
            $notes = "Payment via {$paymentMethod->name}";
            break;
    }

    OrderPayment::create([
        'id' => Str::uuid(),
        'order_id' => $order->id,
        'payment_method_id' => $paymentMethod->id,
        'payment_status' => PaymentStatus::Pending,
        'amount' => $order->total_amount,
        'paid_at' => null,
        'notes' => $notes,
    ]);
}

}
