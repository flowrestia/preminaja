<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Runs in ALL environments to ensure Spatie roles and permissions are always established.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define standard roles
        $superadmin = Role::firstOrCreate(['name' => UserRole::SUPERADMIN->value, 'guard_name' => 'web']);
        $seller = Role::firstOrCreate(['name' => UserRole::SELLER->value, 'guard_name' => 'web']);
        $buyer = Role::firstOrCreate(['name' => UserRole::BUYER->value, 'guard_name' => 'web']);

        // 2. Define platform permissions
        $permissions = [
            // Admin permissions
            'access_admin_panel',
            'manage_users',
            'manage_sellers',
            'manage_categories',
            'manage_products',
            'manage_vouchers',
            'manage_disputes',
            'manage_withdrawals',
            'manage_settings',

            // Seller permissions
            'access_seller_center',
            'manage_own_products',
            'manage_own_credentials',
            'manage_own_withdrawals',
            'reply_reviews',

            // Buyer permissions
            'place_orders',
            'create_reviews',
            'open_tickets',
            'open_disputes',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // 3. Assign default permission sets
        $superadmin->syncPermissions(Permission::all());

        $seller->syncPermissions([
            'access_seller_center',
            'manage_own_products',
            'manage_own_credentials',
            'manage_own_withdrawals',
            'reply_reviews',
            'open_tickets',
        ]);

        $buyer->syncPermissions([
            'place_orders',
            'create_reviews',
            'open_tickets',
            'open_disputes',
        ]);
    }
}
