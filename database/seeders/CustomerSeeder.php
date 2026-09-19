<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Budi Santoso',
                'phone' => '081234567890',
                'email' => 'budi.santoso@example.com',
                'address' => 'Jl. Sudirman No. 45, Jakarta Selatan',
            ],
            [
                'name' => 'Siti Rahmawati',
                'phone' => '081298765432',
                'email' => 'siti.rahma@example.com',
                'address' => 'Jl. Gatot Subroto Kav. 12, Jakarta Selatan',
            ],
            [
                'name' => 'Ahmad Dahlan',
                'phone' => '081311223344',
                'email' => 'ahmad.dahlan@example.com',
                'address' => 'Jl. Malioboro No. 8, Yogyakarta',
            ],
            [
                'name' => 'Dewi Anggraini',
                'phone' => '081355667788',
                'email' => 'dewi.anggraini@example.com',
                'address' => 'Jl. Diponegoro No. 20, Bandung',
            ],
            [
                'name' => 'Hendra Kurniawan',
                'phone' => '081599887766',
                'email' => 'hendra.kurniawan@example.com',
                'address' => 'Jl. Pemuda No. 101, Semarang',
            ],
            [
                'name' => 'Rina Marlina',
                'phone' => '081622334455',
                'email' => 'rina.marlina@example.com',
                'address' => 'Jl. Basuki Rahmat No. 33, Surabaya',
            ],
            [
                'name' => 'Eko Prasetyo',
                'phone' => '081733445566',
                'email' => 'eko.prasetyo@example.com',
                'address' => 'Jl. Teuku Umar No. 15, Denpasar',
            ],
            [
                'name' => 'Putri Handayani',
                'phone' => '082166778899',
                'email' => 'putri.handayani@example.com',
                'address' => 'Jl. Veteran No. 7, Malang',
            ],
        ];

        foreach ($customers as $data) {
            Customer::firstOrCreate(['phone' => $data['phone']], $data);
        }
    }
}
