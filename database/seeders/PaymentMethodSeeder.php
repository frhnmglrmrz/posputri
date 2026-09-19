<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            [
                'code' => 'cash',
                'name' => 'Tunai (Cash)',
                'offline_available' => true,
                'is_active' => true,
            ],
            [
                'code' => 'debit',
                'name' => 'Kartu Debit',
                'offline_available' => true,
                'is_active' => true,
            ],
            [
                'code' => 'credit',
                'name' => 'Kartu Kredit',
                'offline_available' => true,
                'is_active' => true,
            ],
            [
                'code' => 'transfer',
                'name' => 'Transfer Bank',
                'offline_available' => true,
                'is_active' => true,
            ],
            [
                'code' => 'qris',
                'name' => 'QRIS Dinamis (Online Only)',
                'offline_available' => false,
                'is_active' => true,
            ],
            [
                'code' => 'other',
                'name' => 'Lainnya',
                'offline_available' => true,
                'is_active' => true,
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::firstOrCreate(
                ['code' => $method['code']],
                $method
            );
        }
    }
}
