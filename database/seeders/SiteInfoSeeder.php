<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SiteInfo;

class SiteInfoSeeder extends Seeder
{
    public function run(): void
    {
        SiteInfo::updateOrCreate(
        ['id' => '00000000-0000-0000-0000-000000000001'], // Only use ID here
        [
            'site_name' => 'Betamore Limited',
            'logo_url' => '/favicon.svg',
            'favicon_url' => '/favicon.svg',
            'prefix' => [
                'admin' => 'AID',
                'customer' => 'CID',
                'order' => 'ORD',
                'item' => 'ITM',
                'company' => 'COM',
                'promotion' => 'PID',
                'banner' => 'BID',
                'delivery' => 'DID'
            ],
            'meta_description' => 'Betamore Admin Portal offers a comprehensive set of features designed to streamline business operations. It includes User Management with Roles and Permissions for secure access control, Order Tracking to monitor customer purchases in real time, and powerful tools to manage Products, Brands, and Categories for efficient catalog organization.',
        ]
      );
    }
}
