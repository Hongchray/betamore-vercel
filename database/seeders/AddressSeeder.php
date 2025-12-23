<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please seed users first.');
            return;
        }

        $cambodiaAddresses = [
            ['name' => 'Work', 'address' => 'No. 123, Street 271', 'city' => 'Phnom Penh', 'postal_code' => '12000', 'latitude' => '11.5449', 'longitude' => '104.8922'],
            ['name' => 'Office', 'address' => 'National Road 5, Toul Sangke', 'city' => 'Phnom Penh', 'postal_code' => '12000', 'latitude' => '11.5762', 'longitude' => '104.9135'],
            ['name' => 'Home', 'address' => 'Street 1, Kampong Cham', 'city' => 'Kampong Cham', 'postal_code' => '03000', 'latitude' => '11.9934', 'longitude' => '105.4635'],
            ['name' => 'Office', 'address' => 'Angkor Wat Road', 'city' => 'Siem Reap', 'postal_code' => '17000', 'latitude' => '13.3618', 'longitude' => '103.8606'],
            ['name' => 'Work', 'address' => 'Street 6A, Battambang', 'city' => 'Battambang', 'postal_code' => '02000', 'latitude' => '13.0957', 'longitude' => '103.2022'],
        ];

        foreach ($users as $user) {
            for ($i = 0; $i < 3; $i++) {
                $data = $cambodiaAddresses[array_rand($cambodiaAddresses)];

                Address::create([
                    'id' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'contact_name' => $data['name'],
                    'address' => $data['address'],
                    'city' => $data['city'],
                    'postal_code' => $data['postal_code'],
                    'phone' => '012' . rand(100000, 999999),
                    'lattitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                ]);
            }
        }
    }
}
