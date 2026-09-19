<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            OutletSeeder::class,
            AdminSeeder::class,
            PaymentMethodSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            SettingSeeder::class,
            CustomerSeeder::class,
            ShiftSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}
