<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $featuresWithCrud = [
            'user',
            'role',
            'customer',
            'banner',
            'company',
            'item',
            'order',
            'payment',
            'promotion',
            'delivery'
        ];

        $extraPermissions = [
            'dashboard.view',
            'settings.view',
            'settings.edit',
            'settings.delete',
            'website.edit',
        ];

        // Build all permissions list
        $permissions = [];
        foreach ($featuresWithCrud as $feature) {
            foreach (['view', 'create', 'edit', 'delete'] as $action) {
                $permissions[] = "$feature.$action";
            }
        }
        $permissions = array_merge($permissions, $extraPermissions);

        // Create permissions with guard_name 'web'
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Create roles with guard_name 'web'
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $moderatorRole = Role::firstOrCreate(['name' => 'Moderator', 'guard_name' => 'web']);

        // Assign all permissions to Super Admin
        $superAdminRole->syncPermissions(Permission::all());

        // Admin permissions subset
        $adminPermissions = array_filter($permissions, function ($p) {
            return str_starts_with($p, 'user.')
                || str_starts_with($p, 'settings.')
                || in_array($p, ['dashboard.view']);
        });
        $adminRole->syncPermissions($adminPermissions);

        // Moderator permissions subset
        $moderatorPermissions = array_filter($permissions, function ($p) {
            return in_array($p, ['user.view', 'user.edit', 'dashboard.view']);
        });
        $moderatorRole->syncPermissions($moderatorPermissions);
    }
}
