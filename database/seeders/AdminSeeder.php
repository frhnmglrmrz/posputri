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

        // 1. Primary Berkah Mart Accounts
        $accounts = [
            [
                'email' => 'admin@berkahmart.test',
                'name' => 'Administrator',
                'role' => 'Admin',
            ],
            [
                'email' => 'supervisor@berkahmart.test',
                'name' => 'Supervisor Toko',
                'role' => 'Supervisor',
            ],
            [
                'email' => 'kasir@berkahmart.test',
                'name' => 'Kasir 01',
                'role' => 'Cashier',
            ],
            [
                'email' => 'gudang@berkahmart.test',
                'name' => 'Staff Gudang',
                'role' => 'Inventory',
            ],
            // Fallback accounts for legacy sessions/tests
            [
                'email' => 'admin@posputri.test',
                'name' => 'Administrator',
                'role' => 'Admin',
            ],
            [
                'email' => 'supervisor@posputri.test',
                'name' => 'Supervisor Toko',
                'role' => 'Supervisor',
            ],
            [
                'email' => 'kasir@posputri.test',
                'name' => 'Kasir 01',
                'role' => 'Cashier',
            ],
            [
                'email' => 'gudang@posputri.test',
                'name' => 'Staff Gudang',
                'role' => 'Inventory',
            ],
        ];

        foreach ($accounts as $acc) {
            $user = User::updateOrCreate(
                ['email' => $acc['email']],
                [
                    'name' => $acc['name'],
                    'password' => Hash::make('password'),
                    'outlet_id' => $outlet?->id,
                    'is_active' => true,
                ]
            );
            $user->syncRoles([$acc['role']]);
        }
    }
}
