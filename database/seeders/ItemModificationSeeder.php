<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemModification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ItemModificationSeeder extends Seeder
{
    public function run(): void
    {
        $items = Item::all();

        foreach ($items as $item) {
            for ($j = 1; $j <= 3; $j++) {
                ItemModification::create([
                    'id' => Str::uuid(),
                    'item_id' => $item->id,
                    'modification_name' => "Mod $j for " . $item->name,
                    'unit' => 'kg',
                    'unit_price' => rand(1, 100),
                ]);
            }
        }
    }
}
