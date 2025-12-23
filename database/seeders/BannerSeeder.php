<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('banners')->insert([
            [
                'id' => (string) Str::uuid(),
                'name' => '60% OFF',
                'banner_id' => 'BID0000000001',
                'banner_image' => 'https://focuz-staging-space.sgp1.digitaloceanspaces.com/betamore/1754895374_360_F_249501541_XmWdfAfUbWAvGxBwAM0ba2aYT36ntlpH.jpg',
                'description' => 'Huge discounts on summer collections!',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Free Delivery',
                'banner_id' => 'BID0000000002',
                'banner_image' => 'https://focuz-staging-space.sgp1.digitaloceanspaces.com/betamore/1754900014_online-shopping-on-website-e-commerce-applications-and-digital-marketing-hand-holding-smartphonwith-the-delivery-man-template-for-banner-web-landing-page-social-media-flat-design-concept-vector.jpg',
                'description' => 'Check out the latest arrivals in our store.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Buy 2 Get 1 Free',
                'banner_id' => 'BID0000000003',
                'banner_image' => 'https://focuz-staging-space.sgp1.digitaloceanspaces.com/betamore/1754971240_stock-vector-free-prize-d-giveaway-gifts-shopping-offer-trendy-banner-buy-get-bonus-box-promo-ads-leaflet-2169479251.jpg',
                'description' => 'Buy 1 will be free one',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
