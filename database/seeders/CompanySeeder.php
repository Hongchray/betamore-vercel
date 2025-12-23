<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('companies')->insert([
            [
                'id' => Str::uuid(),
                'company_id' => 'COM0000000001',
                'name_en' => 'Focuz Solution',
                'name_km' => 'Focuz Solution',
                'description_en' => 'Technology and Innovation Services',
                'description_km' => 'Technology and Innovation Services',
                'logo' => 'https://focuz-staging-space.sgp1.digitaloceanspaces.com/betamore/1754971414_1751431469_6864b92d6c422.jpg',
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'company_id' => 'COM0000000002',
                'name_en' => 'Tube Cafe',
                'name_km' => 'កាហ្វេ ធូប',
                'description_en' => 'TUBE coffeecambodia Tube Coffee is a Cambodian coffee shop...',
                'description_km' => 'TUBE coffeecambodia Tube Coffee is a Cambodian coffee shop...',
                'logo' => 'https://focuz-staging-space.sgp1.digitaloceanspaces.com/betamore/1754971370_tube.jpeg',
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
