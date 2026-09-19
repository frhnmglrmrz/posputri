<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan Siap Saji', 'slug' => 'makanan-siap-saji'],
            ['name' => 'Kopi & Minuman', 'slug' => 'kopi-minuman'],
            ['name' => 'Snack & Biskuit', 'slug' => 'snack-biskuit'],
            ['name' => 'Sembako & Kebutuhan', 'slug' => 'sembako-kebutuhan'],
            ['name' => 'Perawatan Diri', 'slug' => 'perawatan-diri'],
            ['name' => 'Alat Tulis & Kantor', 'slug' => 'alat-tulis-kantor'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => $cat['slug']],
                ['name' => $cat['name'], 'is_active' => true]
            );
        }
    }
}
