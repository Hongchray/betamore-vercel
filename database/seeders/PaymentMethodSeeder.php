<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        // Create Payment Methods
        $bankTransferMethod = DB::table('payment_methods')->insertGetId([
            'id' => Str::uuid(),
            'name' => 'ABA Payway',
            'type' => 'abapay_khqr',
            'description' => 'Transfer money directly to our bank account',
            'logo' => null,
            'is_active' => DB::raw('true'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cardPaymentMethod = DB::table('payment_methods')->insertGetId([
            'id' => Str::uuid(),
            'name' => 'Credit/Debit Card',
            'type' => 'cards',
            'description' => 'Pay with your credit or debit card',
            'logo' => null,
            'is_active' => DB::raw('true'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $codMethod = DB::table('payment_methods')->insertGetId([
            'id' => Str::uuid(),
            'name' => 'Cash on Delivery',
            'type' => 'cash_on_delivery',
            'description' => 'Pay when you receive your order',
            'logo' => null,
            'is_active' => DB::raw('true'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Get the actual UUIDs for foreign keys
    }
}
