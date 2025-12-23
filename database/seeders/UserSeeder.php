<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\UserType;
use App\Enums\Gender;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin users data
        $adminUsers = [
            [
                'user_id' => 'AID0000000001',
                'first_name' => 'Focuz',
                'last_name' => 'Squad',
                'image' => 'https://focuz-staging-space.sgp1.digitaloceanspaces.com/betamore/1754971414_1751431469_6864b92d6c422.jpg',
                'gender' => Gender::MALE->value,
                'phone' => '097 123 654',
                'telegram' => null,
                'type' => UserType::ADMIN->value,
                'email' => 'hello@focuzsolution.com',
                'password' => Hash::make('b4tamore@pp'),
            ],
            
        ];

        foreach ($adminUsers as $index => $adminData) {
            $adminUser = User::firstOrCreate(
                ['email' => $adminData['email']],
                array_merge($adminData, [
                    'email_verified_at' => now(),
                    'phone_verified_at' => now(),
                ])
            );

            if (!$adminUser->hasRole('Super Admin') && $index === 0) {
                $adminUser->assignRole('Super Admin');
            } elseif (!$adminUser->hasRole('Admin') && $index === 1) {
                $adminUser->assignRole('Admin');
            }
        }

        // Existing Customer creation logic (unchanged)
        $customers = [
            [
                'user_id' => 'CID0000000001',
                'first_name' => 'Song',
                'last_name' => 'Hongchray',
                'gender' => Gender::MALE->value,
                'image' => 'https://focuz-staging-space.sgp1.digitaloceanspaces.com/betamore/1754971791_90f99396412b31bcf4ce3974000915ca.jpg',
                'email' => 'songhongchray@example.com',
                'phone' => '0976168988',
                'telegram' => '@chrayyyy',
                'type' => UserType::CUSTOMER->value,
                'password' => Hash::make('password123'),
            ],
            [
                'user_id' => 'CID0000000002',
                'first_name' => 'New',
                'last_name' => 'User',
                'gender' => Gender::MALE->value,
                'image' => 'https://focuz-staging-space.sgp1.digitaloceanspaces.com/betamore/1754971808_business-man-cartoon-character-vector.jpg',
                'email' => 'newuser@example.com',
                'phone' => '0964519227',
                'telegram' => '',
                'type' => UserType::CUSTOMER->value,
                'password' => Hash::make('password123'),
            ],
            
        ];


        foreach ($customers as $customerData) {
            User::firstOrCreate(
                ['phone' => $customerData['phone']],
                array_merge($customerData, [
                    'email_verified_at' => $customerData['email'] ? now() : null,
                    'phone_verified_at' => now(),
                ])
            );
        }
    }

}
