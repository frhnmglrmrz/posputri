<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Outlet;
use Illuminate\Database\Seeder;

class OutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlet = Outlet::firstOrCreate(
            ['code' => 'OUT-001'],
            [
                'name' => 'Outlet Utama',
                'address' => 'Jl. Boulevard Raya No. 1, Jakarta',
                'phone' => '021-5551234',
                'is_active' => true,
            ]
        );

        Device::firstOrCreate(
            ['name' => 'POS Kasir 01', 'outlet_id' => $outlet->id],
            [
                'status' => 'active',
                'last_seen_at' => now(),
            ]
        );

        Device::firstOrCreate(
            ['name' => 'POS Kasir 02', 'outlet_id' => $outlet->id],
            [
                'status' => 'active',
                'last_seen_at' => now(),
            ]
        );
    }
}
