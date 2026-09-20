<?php

namespace Database\Seeders;

use App\Models\Outlet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlet = Outlet::first();

        // 1. Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@posputri.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'outlet_id' => $outlet?->id,
                'is_active' => true,
            ]
        );
        $admin->syncRoles(['Admin']);

        // 2. Supervisor
        $supervisor = User::firstOrCreate(
            ['email' => 'supervisor@posputri.test'],
            [
                'name' => 'Supervisor Toko',
                'password' => Hash::make('password'),
                'outlet_id' => $outlet?->id,
                'is_active' => true,
            ]
        );
        $supervisor->syncRoles(['Supervisor']);

        // 3. Cashier
        $cashier = User::firstOrCreate(
            ['email' => 'kasir@posputri.test'],
            [
                'name' => 'Kasir 01',
                'password' => Hash::make('password'),
                'outlet_id' => $outlet?->id,
                'is_active' => true,
            ]
        );
        $cashier->syncRoles(['Cashier']);

        // 4. Inventory / Staff Gudang (Input Barang & Stok)
        $inventory = User::firstOrCreate(
            ['email' => 'gudang@posputri.test'],
            [
                'name' => 'Staff Gudang',
                'password' => Hash::make('password'),
                'outlet_id' => $outlet?->id,
                'is_active' => true,
            ]
        );
        $inventory->syncRoles(['Inventory']);
    }
}
