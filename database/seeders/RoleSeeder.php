<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cashierPermissions = [
            'access-pos',
            'open-shift',
            'close-shift',
            'search-product',
            'scan-barcode',
            'checkout',
            'receive-payment',
            'print-receipt',
            'view-current-shift-transactions',
        ];

        $supervisorPermissions = array_merge($cashierPermissions, [
            'view-transactions',
            'void-transaction',
            'view-inventory',
            'adjust-stock',
            'view-shifts',
            'view-basic-reports',
        ]);

        $inventoryPermissions = [
            'search-product',
            'scan-barcode',
            'manage-products',
            'manage-categories',
            'manage-inventory',
            'manage-prices',
            'view-inventory',
            'adjust-stock',
        ];

        $cashierRole = Role::firstOrCreate(['name' => 'Cashier', 'guard_name' => 'web']);
        $cashierRole->syncPermissions($cashierPermissions);

        $inventoryRole = Role::firstOrCreate(['name' => 'Inventory', 'guard_name' => 'web']);
        $inventoryRole->syncPermissions($inventoryPermissions);

        $supervisorRole = Role::firstOrCreate(['name' => 'Supervisor', 'guard_name' => 'web']);
        $supervisorRole->syncPermissions($supervisorPermissions);

        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());
    }
}
