<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Cashier permissions
            'access-pos',
            'open-shift',
            'close-shift',
            'search-product',
            'scan-barcode',
            'checkout',
            'receive-payment',
            'print-receipt',
            'view-current-shift-transactions',

            // Supervisor permissions
            'view-transactions',
            'void-transaction',
            'view-inventory',
            'adjust-stock',
            'view-shifts',
            'view-basic-reports',

            // Admin permissions
            'manage-users',
            'manage-products',
            'manage-categories',
            'manage-inventory',
            'manage-prices',
            'manage-outlets',
            'manage-devices',
            'manage-roles',
            'view-reports',
            'configure-pos',
            'view-sync-problems',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }
}
