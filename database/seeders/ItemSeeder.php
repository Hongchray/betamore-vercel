<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $companyIds = DB::table('companies')->pluck('id');

        if ($companyIds->isEmpty()) {
            $this->command->warn('No companies found. Please seed the companies table first.');
            return;
        }

        $items = [
            [
                'name_en' => 'Classic Cotton T-Shirt',
                'name_km' => 'អាវយឺតជែលខុងតុងបុរាណ',
                'description_en' => 'Soft and breathable cotton t-shirt suitable for everyday wear.',
                'description_km' => 'អាវយឺតខុងតុងទន់និងអាចដកដង្ហើមបាន សមរម្យសម្រាប់ពាក់ប្រចាំថ្ងៃ។',
                'images' => [
                    'https://focuz-staging-space.sgp1.digitaloceanspaces.com/betamore/1754971569_tshirtcottonback.webp',
                    'https://focuz-staging-space.sgp1.digitaloceanspaces.com/betamore/1754971578_B1pppR4gVKL._CLa_2140,2000_91OsoWUJ-NL.png_0,0,2140,2000+0.0,0.0,2140.0,2000.0_UY1000_.jpg',

                ],
            ],
            [
                'name_en' => 'JBL Wireless Bluetooth Headphones',
                'name_km' => 'កាសប៊្លូធូសឥតខ្សែ JBL',
                'description_en' => 'Noise-cancelling headphones with long-lasting battery life.',
                'description_km' => 'កាសបំបាត់សំឡេងរំខាន និងអាចប្រើបានយូរ។',
                'images' => [
                    'https://focuz-staging-space.sgp1.digitaloceanspaces.com/betamore/1754971461_01.webp',
                    'https://focuz-staging-space.sgp1.digitaloceanspaces.com/betamore/1753764019_JBL_E55BT_KEY_BLACK_6175_FS_x1-1605x1605px.png',
                ],
            ],
            [
                'name_en' => 'Stainless Steel Water Bottle',
                'name_km' => 'ដបទឹកដែកអ៊ីណុក',
                'description_en' => 'Durable and insulated water bottle keeps drinks cold or hot for hours.',
                'description_km' => 'ដបទឹកដែកអ៊ីណុកធន់និងរក្សាសីតុណ្ហភាពបានយូរ។',
                'images' => [
                    'https://focuz-staging-space.sgp1.digitaloceanspaces.com/items/1752046093_686e1a0db9f3a.',
                    'https://focuz-staging-space.sgp1.digitaloceanspaces.com/items/1752046079_686e19ff02b04.',
                    'https://focuz-staging-space.sgp1.digitaloceanspaces.com/items/1752046153_686e1a4916ad7.',
                ],
            ],
            [
                'name_en' => 'Ergonomic Office Chair',
                'name_km' => 'កៅអីការិយាល័យបែបអ៊ឺរ៉ូណូមិច',
                'description_en' => 'Comfortable chair with adjustable height and lumbar support.',
                'description_km' => 'កៅអីមានផ្នែកគាំខ្នងនិងកម្រិតបង្រួមបានផ្ដល់ភាពស្រួល។',
                'images' => [
                    'https://focuz-staging-space.sgp1.digitaloceanspaces.com/items/1752046354_686e1b127594a.',
                    'https://focuz-staging-space.sgp1.digitaloceanspaces.com/items/1752046344_686e1b088db85.',
                ],
            ],
            [
                'name_en' => 'Bluetooth Portable Speaker',
                'name_km' => 'ម៉ាស៊ីនភ្លេងប៊្លូធូសចល័ត',
                'description_en' => 'Compact speaker with high-quality sound and long battery life.',
                'description_km' => 'ម៉ាស៊ីនភ្លេងតូចមានសំឡេងល្អ និងអាចប្រើបានយូរ។',
                'images' => [
                    'https://focuz-staging-space.sgp1.digitaloceanspaces.com/items/1752046616_686e1c182399d.',
                    'https://focuz-staging-space.sgp1.digitaloceanspaces.com/items/1752046602_686e1c0ad32f9.',
                ],
            ],
            [
                'name_en' => 'Yoga Mat',
                'name_km' => 'ព្រំយូហ្គា',
                'description_en' => 'Non-slip yoga mat with comfortable cushioning for all exercises.',
                'description_km' => 'ព្រំយូហ្គាមិនរអិល មានជម្រៅសម្រាប់ហាត់ប្រាណបានល្អ។',
                'images' => [
                    'https://focuz-staging-space.sgp1.digitaloceanspaces.com/items/1752046668_686e1c4c6a5ed.',
                    'https://focuz-staging-space.sgp1.digitaloceanspaces.com/items/1752046676_686e1c5422360.',
                ],
            ],
        ];

        foreach ($items as $index => $itemData) {
            $item = Item::create([
                'id' => Str::uuid(),
                'item_id' => 'ITM' . str_pad($index + 1, 10, '0', STR_PAD_LEFT),
                'name_en' => $itemData['name_en'],
                'name_km' => $itemData['name_km'],
                'description_en' => $itemData['description_en'],
                'description_km' => $itemData['description_km'],
                'company_id' => $companyIds->random(),
            ]);

            foreach ($itemData['images'] as $imgIndex => $imageUrl) {
                DB::table('item_images')->insert([
                    'id' => Str::uuid(),
                    'item_id' => $item->id,
                    'image' => $imageUrl,
                    'is_main' => $imgIndex === 0, // first image is the main
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
