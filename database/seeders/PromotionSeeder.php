<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Promotion;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        $items = Item::take(5)->get();

        if ($items->isEmpty()) {
            $this->command->warn('No items found. Skipping promotion seeding.');
            return;
        }

        // Generate UUID manually to ensure promotion ID is valid
        $promotionId = Str::uuid()->toString();

        $promotion = Promotion::create([
            'id' => $promotionId,
            'promotion_id' => 'PID0000000001',
            'banner' => 'https://focuz-staging-space.sgp1.digitaloceanspaces.com/betamore/1754971685_end-of-year-sale-banner-template-design-big-sale-event-on-red-background-social-media-shopping-online-vector.jpg',
            'name_en' => 'End Year Promotion!',
            'name_km' => 'ប្រូម៉ូសិនចុងឆ្នាំ!',
            'description_en' => 'Enjoy 20% off during end of year sale!',
            'description_km' => 'រីករាយជាមួយការបញ្ចុះតម្លៃ 20% ក្នុងអំឡុងពេលលក់ចុងឆ្នាំ!',
            'type' => 'percent',
            'discount_value' => 20,
            'start_date' => now(),
            'end_date' => now()->addWeeks(2),

        ]);

        foreach ($items as $item) {
            DB::table('item_promotion')->insert([
                'item_id' => $item->id,
                'promotion_id' => $promotion->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Promotion and related item promotions seeded successfully.');
    }
}
