<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bank;
use Illuminate\Support\Str;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        $banks = [
            'ABA Bank',
            'ACLEDA Bank',
            'Canadia Bank',
            'Wing Bank',
        ];

        foreach ($banks as $bankName) {
            Bank::create([
                'id' => Str::uuid(),
                'name' => $bankName,
            ]);
        }
    }
}
