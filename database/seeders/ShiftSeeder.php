<?php

namespace Database\Seeders;

use App\Enums\ShiftStatus;
use App\Models\Device;
use App\Models\Outlet;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlet = Outlet::first();
        $cashier = User::where('email', 'kasir@posputri.test')->first();
        $device = Device::where('outlet_id', $outlet?->id)->first();

        if (! $outlet || ! $cashier) {
            return;
        }

        // 1. Shift Kemarin (Closed)
        Shift::firstOrCreate(
            [
                'outlet_id' => $outlet->id,
                'cashier_id' => $cashier->id,
                'opened_at' => Carbon::yesterday()->setHour(8)->setMinute(0)->setSecond(0),
            ],
            [
                'device_id' => $device?->id,
                'opening_cash' => 500000,
                'expected_cash' => 1250000,
                'closing_cash' => 1250000,
                'difference' => 0,
                'closed_at' => Carbon::yesterday()->setHour(17)->setMinute(0)->setSecond(0),
                'status' => ShiftStatus::Closed,
                'notes' => 'Shift kemarin selesai, kas seimbang.',
            ]
        );

        // 2. Shift Hari Ini (Open)
        Shift::firstOrCreate(
            [
                'outlet_id' => $outlet->id,
                'cashier_id' => $cashier->id,
                'opened_at' => Carbon::today()->setHour(8)->setMinute(30)->setSecond(0),
            ],
            [
                'device_id' => $device?->id,
                'opening_cash' => 500000,
                'status' => ShiftStatus::Open,
                'notes' => 'Shift kasir pagi aktif.',
            ]
        );
    }
}
