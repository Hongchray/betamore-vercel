<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('deliveries')->insert([
            [
                'id' => Str::uuid(),
                'delivery_id' => 'DID0000000001',
                'name' => 'Vireak Buntham Express',
                'image' => 'https://focuz-staging-space.sgp1.digitaloceanspaces.com/betamore/1754972137_vireak-buntham.3087fdaf.png',
                'shipping_fee' => '1',
                'description' => 'Fast international delivery service.',
                'is_active' => DB::raw('true'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'delivery_id' => 'DID0000000002',
                'name' => 'FedEx',
                'image' => 'https://focuz-staging-space.sgp1.digitaloceanspaces.com/betamore/1754972096_logo-fedex.png',
                'shipping_fee' => '2',
                'description' => 'Reliable global shipping.',
                'is_active' => DB::raw('true'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'delivery_id' => 'DID0000000003',
                'name' => 'J&T',
                'image' => 'https://focuz-staging-space.sgp1.digitaloceanspaces.com/betamore/1754972062_photo_2024-04-25_17-12-03.jpg',
                'shipping_fee' => '1.5',
                'description' => 'United Parcel Service delivery.',
                'is_active' => DB::raw('true'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
