<?php

namespace Database\Seeders;

use App\Enums\StockMovementType;
use App\Models\Category;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all()->keyBy('slug');
        $outlet = Outlet::first();

        $rawProducts = [
            // Makanan Siap Saji (18 items)
            ['cat' => 'makanan-siap-saji', 'name' => 'Nasi Goreng Spesial', 'purchase' => 15000, 'selling' => 25000],
            ['cat' => 'makanan-siap-saji', 'name' => 'Nasi Goreng Ayam', 'purchase' => 14000, 'selling' => 22000],
            ['cat' => 'makanan-siap-saji', 'name' => 'Mie Goreng Jawa', 'purchase' => 12000, 'selling' => 20000],
            ['cat' => 'makanan-siap-saji', 'name' => 'Mie Kuah Kari Ayam', 'purchase' => 12000, 'selling' => 20000],
            ['cat' => 'makanan-siap-saji', 'name' => 'Ayam Geprek Sambal Bawang', 'purchase' => 13000, 'selling' => 22000],
            ['cat' => 'makanan-siap-saji', 'name' => 'Ayam Bakar Madu', 'purchase' => 15000, 'selling' => 25000],
            ['cat' => 'makanan-siap-saji', 'name' => 'Bebek Goreng Crispy', 'purchase' => 22000, 'selling' => 35000],
            ['cat' => 'makanan-siap-saji', 'name' => 'Soto Ayam Lamongan', 'purchase' => 12000, 'selling' => 18000],
            ['cat' => 'makanan-siap-saji', 'name' => 'Rawon Daging Sapi', 'purchase' => 20000, 'selling' => 32000],
            ['cat' => 'makanan-siap-saji', 'name' => 'Sop Buntut Sapi', 'purchase' => 30000, 'selling' => 48000],
            ['cat' => 'makanan-siap-saji', 'name' => 'Nasi Uduk Betawi Komplit', 'purchase' => 14000, 'selling' => 22000],
            ['cat' => 'makanan-siap-saji', 'name' => 'Nasi Kuning Cakalang', 'purchase' => 15000, 'selling' => 24000],
            ['cat' => 'makanan-siap-saji', 'name' => 'Roti Bakar Cokelat Keju', 'purchase' => 10000, 'selling' => 18000],
            ['cat' => 'makanan-siap-saji', 'name' => 'Pisang Goreng Keju', 'purchase' => 8000, 'selling' => 15000],
            ['cat' => 'makanan-siap-saji', 'name' => 'Kentang Goreng Bolognese', 'purchase' => 11000, 'selling' => 18000],
            ['cat' => 'makanan-siap-saji', 'name' => 'Dimsum Ayam Udang 4pcs', 'purchase' => 12000, 'selling' => 20000],
            ['cat' => 'makanan-siap-saji', 'name' => 'Siomay Bandung Porsi', 'purchase' => 12000, 'selling' => 20000],
            ['cat' => 'makanan-siap-saji', 'name' => 'Batagor Kuah Spesial', 'purchase' => 12000, 'selling' => 20000],

            // Kopi & Minuman (20 items)
            ['cat' => 'kopi-minuman', 'name' => 'Kopi Susu Gula Aren', 'purchase' => 8000, 'selling' => 18000],
            ['cat' => 'kopi-minuman', 'name' => 'Espresso Single Shot', 'purchase' => 5000, 'selling' => 12000],
            ['cat' => 'kopi-minuman', 'name' => 'Americano Iced', 'purchase' => 6000, 'selling' => 15000],
            ['cat' => 'kopi-minuman', 'name' => 'Cafe Latte Hot', 'purchase' => 9000, 'selling' => 20000],
            ['cat' => 'kopi-minuman', 'name' => 'Cappuccino Iced', 'purchase' => 9000, 'selling' => 20000],
            ['cat' => 'kopi-minuman', 'name' => 'Caramel Macchiato', 'purchase' => 11000, 'selling' => 24000],
            ['cat' => 'kopi-minuman', 'name' => 'Vanilla Latte', 'purchase' => 10000, 'selling' => 22000],
            ['cat' => 'kopi-minuman', 'name' => 'Mocha Frappe', 'purchase' => 12000, 'selling' => 25000],
            ['cat' => 'kopi-minuman', 'name' => 'Matcha Latte Green Tea', 'purchase' => 11000, 'selling' => 22000],
            ['cat' => 'kopi-minuman', 'name' => 'Thai Milk Tea Cold', 'purchase' => 8000, 'selling' => 16000],
            ['cat' => 'kopi-minuman', 'name' => 'Taro Milkshake', 'purchase' => 9000, 'selling' => 18000],
            ['cat' => 'kopi-minuman', 'name' => 'Red Velvet Latte', 'purchase' => 10000, 'selling' => 20000],
            ['cat' => 'kopi-minuman', 'name' => 'Chocolate Signature Hot', 'purchase' => 9000, 'selling' => 18000],
            ['cat' => 'kopi-minuman', 'name' => 'Lemon Tea Iced Segar', 'purchase' => 5000, 'selling' => 12000],
            ['cat' => 'kopi-minuman', 'name' => 'Lychee Tea with Jelly', 'purchase' => 7000, 'selling' => 16000],
            ['cat' => 'kopi-minuman', 'name' => 'Peach Sparkling Tea', 'purchase' => 8000, 'selling' => 18000],
            ['cat' => 'kopi-minuman', 'name' => 'Jus Alpukat Kocok', 'purchase' => 9000, 'selling' => 18000],
            ['cat' => 'kopi-minuman', 'name' => 'Jus Mangga Manis', 'purchase' => 8000, 'selling' => 16000],
            ['cat' => 'kopi-minuman', 'name' => 'Air Mineral Botol 600ml', 'purchase' => 2500, 'selling' => 5000],
            ['cat' => 'kopi-minuman', 'name' => 'Teh Botol Kotak 250ml', 'purchase' => 3000, 'selling' => 6000],

            // Snack & Biskuit (20 items)
            ['cat' => 'snack-biskuit', 'name' => 'Oreo Vanilla 133g', 'purchase' => 8500, 'selling' => 11500],
            ['cat' => 'snack-biskuit', 'name' => 'Oreo Chocolate Cream 133g', 'purchase' => 8500, 'selling' => 11500],
            ['cat' => 'snack-biskuit', 'name' => 'Tango Wafer Cokelat 176g', 'purchase' => 9000, 'selling' => 12000],
            ['cat' => 'snack-biskuit', 'name' => 'Tango Wafer Vanilla 176g', 'purchase' => 9000, 'selling' => 12000],
            ['cat' => 'snack-biskuit', 'name' => 'Pocky Strawberry 45g', 'purchase' => 7500, 'selling' => 10000],
            ['cat' => 'snack-biskuit', 'name' => 'Pocky Chocolate 47g', 'purchase' => 7500, 'selling' => 10000],
            ['cat' => 'snack-biskuit', 'name' => 'Chitato Sapi Panggang 68g', 'purchase' => 9500, 'selling' => 13000],
            ['cat' => 'snack-biskuit', 'name' => 'Chitato Ayam Bumbu 68g', 'purchase' => 9500, 'selling' => 13000],
            ['cat' => 'snack-biskuit', 'name' => 'Lay Potato Chips Rumput Laut', 'purchase' => 10000, 'selling' => 13500],
            ['cat' => 'snack-biskuit', 'name' => 'Qtela Singkong Balado 180g', 'purchase' => 11000, 'selling' => 15000],
            ['cat' => 'snack-biskuit', 'name' => 'Kusuka Keripik Singkong BBQ', 'purchase' => 6000, 'selling' => 8500],
            ['cat' => 'snack-biskuit', 'name' => 'SilverQueen Almond 58g', 'purchase' => 12500, 'selling' => 16500],
            ['cat' => 'snack-biskuit', 'name' => 'SilverQueen Cashew 58g', 'purchase' => 12500, 'selling' => 16500],
            ['cat' => 'snack-biskuit', 'name' => 'KitKat 4 Finger Chocolate', 'purchase' => 9000, 'selling' => 12500],
            ['cat' => 'snack-biskuit', 'name' => 'Beng Beng Chocolate Wafer 3x25g', 'purchase' => 5500, 'selling' => 8000],
            ['cat' => 'snack-biskuit', 'name' => 'Roma Kelapa Biskuit 300g', 'purchase' => 8500, 'selling' => 11000],
            ['cat' => 'snack-biskuit', 'name' => 'Roma Malkist Abon Gurih', 'purchase' => 6500, 'selling' => 9000],
            ['cat' => 'snack-biskuit', 'name' => 'Selamat Wafer Chocolate 198g', 'purchase' => 12000, 'selling' => 16000],
            ['cat' => 'snack-biskuit', 'name' => 'Richeese Nabati Wafer Keju 168g', 'purchase' => 7500, 'selling' => 10500],
            ['cat' => 'snack-biskuit', 'name' => 'Garuda Kacang Kulit Garing 375g', 'purchase' => 21000, 'selling' => 26000],

            // Sembako & Kebutuhan (20 items)
            ['cat' => 'sembako-kebutuhan', 'name' => 'Beras Pandan Wangi 5kg', 'purchase' => 65000, 'selling' => 78000],
            ['cat' => 'sembako-kebutuhan', 'name' => 'Beras Setra Ramos 5kg', 'purchase' => 62000, 'selling' => 74000],
            ['cat' => 'sembako-kebutuhan', 'name' => 'Minyak Goreng Bimoli 2L', 'purchase' => 32000, 'selling' => 38000],
            ['cat' => 'sembako-kebutuhan', 'name' => 'Minyak Goreng Sania 2L', 'purchase' => 31000, 'selling' => 37000],
            ['cat' => 'sembako-kebutuhan', 'name' => 'Minyak Goreng Filma 2L', 'purchase' => 33000, 'selling' => 39000],
            ['cat' => 'sembako-kebutuhan', 'name' => 'Gula Pasir Gulaku Putih 1kg', 'purchase' => 15500, 'selling' => 18500],
            ['cat' => 'sembako-kebutuhan', 'name' => 'Gula Pasir Rose Brand 1kg', 'purchase' => 15000, 'selling' => 18000],
            ['cat' => 'sembako-kebutuhan', 'name' => 'Tepung Terigu Segitiga Biru 1kg', 'purchase' => 11000, 'selling' => 14000],
            ['cat' => 'sembako-kebutuhan', 'name' => 'Tepung Terigu Kunci Biru 1kg', 'purchase' => 11500, 'selling' => 14500],
            ['cat' => 'sembako-kebutuhan', 'name' => 'Garam Beryodium Cap Kapal 250g', 'purchase' => 2500, 'selling' => 4000],
            ['cat' => 'sembako-kebutuhan', 'name' => 'Indomie Goreng Original 85g', 'purchase' => 2800, 'selling' => 3500],
            ['cat' => 'sembako-kebutuhan', 'name' => 'Indomie Kuah Ayam Bawang 69g', 'purchase' => 2700, 'selling' => 3500],
            ['cat' => 'sembako-kebutuhan', 'name' => 'Indomie Soto Mie 70g', 'purchase' => 2700, 'selling' => 3500],
            ['cat' => 'sembako-kebutuhan', 'name' => 'Mie Sedaap Goreng 90g', 'purchase' => 2700, 'selling' => 3500],
            ['cat' => 'sembako-kebutuhan', 'name' => 'Sedaap Mie Kuah Kari Kental', 'purchase' => 2800, 'selling' => 3500],
            ['cat' => 'sembako-kebutuhan', 'name' => 'Kecap Manis Bango Botol 275ml', 'purchase' => 15000, 'selling' => 19000],
            ['cat' => 'sembako-kebutuhan', 'name' => 'Saus Sambal ABC Extra Pedas 335ml', 'purchase' => 12000, 'selling' => 15500],
            ['cat' => 'sembako-kebutuhan', 'name' => 'Saus Tomat ABC Botol 335ml', 'purchase' => 10500, 'selling' => 13500],
            ['cat' => 'sembako-kebutuhan', 'name' => 'Susu Kental Manis Frisian Flag Gold', 'purchase' => 14500, 'selling' => 18000],
            ['cat' => 'sembako-kebutuhan', 'name' => 'Susu UHT Ultra Milk Full Cream 1L', 'purchase' => 17000, 'selling' => 21000],

            // Perawatan Diri (16 items)
            ['cat' => 'perawatan-diri', 'name' => 'Sabun Mandi Lifebuoy Total 10 85g', 'purchase' => 3500, 'selling' => 5000],
            ['cat' => 'perawatan-diri', 'name' => 'Sabun Cair Biore Men Cool 450ml', 'purchase' => 22000, 'selling' => 28000],
            ['cat' => 'perawatan-diri', 'name' => 'Shampoo Clear Men Cool Sport 160ml', 'purchase' => 21000, 'selling' => 26500],
            ['cat' => 'perawatan-diri', 'name' => 'Shampoo Pantene Anti Dandruff 160ml', 'purchase' => 22000, 'selling' => 27500],
            ['cat' => 'perawatan-diri', 'name' => 'Pasta Gigi Pepsodent Fresh Cool 190g', 'purchase' => 11000, 'selling' => 14500],
            ['cat' => 'perawatan-diri', 'name' => 'Sikat Gigi Formula Double Action 3s', 'purchase' => 12000, 'selling' => 16000],
            ['cat' => 'perawatan-diri', 'name' => 'Mouthwash Listerine Cool Mint 250ml', 'purchase' => 22000, 'selling' => 28500],
            ['cat' => 'perawatan-diri', 'name' => 'Deodorant Rexona Men Ice Cool 50ml', 'purchase' => 16000, 'selling' => 21000],
            ['cat' => 'perawatan-diri', 'name' => 'Pembersih Muka Garnier Men Acno 100ml', 'purchase' => 26000, 'selling' => 33000],
            ['cat' => 'perawatan-diri', 'name' => 'Sunscreen Biore UV Aqua Rich 50g', 'purchase' => 38000, 'selling' => 48000],
            ['cat' => 'perawatan-diri', 'name' => 'Hand Sanitizer Dettol Aloe 50ml', 'purchase' => 9000, 'selling' => 12500],
            ['cat' => 'perawatan-diri', 'name' => 'Tisu Basah Mitu Antiseptic 50s', 'purchase' => 11000, 'selling' => 15000],
            ['cat' => 'perawatan-diri', 'name' => 'Tisu Wajah Paseo Smart 250 Sheets', 'purchase' => 12500, 'selling' => 16000],
            ['cat' => 'perawatan-diri', 'name' => 'Kapas Wajah Selection Cotton 50g', 'purchase' => 7000, 'selling' => 9500],
            ['cat' => 'perawatan-diri', 'name' => 'Detergen Rinso Cair Anti Noda 750ml', 'purchase' => 17000, 'selling' => 22000],
            ['cat' => 'perawatan-diri', 'name' => 'Pewangi Molto All in One Blue 720ml', 'purchase' => 18000, 'selling' => 23000],

            // Alat Tulis & Kantor (16 items)
            ['cat' => 'alat-tulis-kantor', 'name' => 'Pulpen Standard AE7 Hitam 0.5', 'purchase' => 2000, 'selling' => 3500],
            ['cat' => 'alat-tulis-kantor', 'name' => 'Pulpen Standard AE7 Biru 0.5', 'purchase' => 2000, 'selling' => 3500],
            ['cat' => 'alat-tulis-kantor', 'name' => 'Pulpen Gel Pilot G2 0.7 Hitam', 'purchase' => 15000, 'selling' => 20000],
            ['cat' => 'alat-tulis-kantor', 'name' => 'Pensil Faber Castell 2B Asli', 'purchase' => 4000, 'selling' => 6000],
            ['cat' => 'alat-tulis-kantor', 'name' => 'Penghapus Faber Castell Hitam Besar', 'purchase' => 3500, 'selling' => 5500],
            ['cat' => 'alat-tulis-kantor', 'name' => 'Tipe-X Kertas Joyko Correction Tape', 'purchase' => 6500, 'selling' => 9500],
            ['cat' => 'alat-tulis-kantor', 'name' => 'Buku Tulis Sinar Dunia 38 Lembar', 'purchase' => 3000, 'selling' => 4500],
            ['cat' => 'alat-tulis-kantor', 'name' => 'Buku Tulis Sinar Dunia 58 Lembar', 'purchase' => 4000, 'selling' => 6000],
            ['cat' => 'alat-tulis-kantor', 'name' => 'Sticky Notes Post-it Kuning 3x3', 'purchase' => 8000, 'selling' => 12000],
            ['cat' => 'alat-tulis-kantor', 'name' => 'Kertas HVS PaperOne A4 75gsm Rim', 'purchase' => 42000, 'selling' => 52000],
            ['cat' => 'alat-tulis-kantor', 'name' => 'Gunting Joyko Sedang Stainless', 'purchase' => 7000, 'selling' => 10500],
            ['cat' => 'alat-tulis-kantor', 'name' => 'Lem Kertas Kering UHU Stic 8.2g', 'purchase' => 6500, 'selling' => 9500],
            ['cat' => 'alat-tulis-kantor', 'name' => 'Stapler Joyko HD-10 Standard', 'purchase' => 11000, 'selling' => 15000],
            ['cat' => 'alat-tulis-kantor', 'name' => 'Isi Staples Joyko No.10 Dus Kecil', 'purchase' => 2000, 'selling' => 3500],
            ['cat' => 'alat-tulis-kantor', 'name' => 'Lakban Bening Daimaru 2 Inch', 'purchase' => 11000, 'selling' => 15000],
            ['cat' => 'alat-tulis-kantor', 'name' => 'Lakban Cokelat Daimaru 2 Inch', 'purchase' => 11000, 'selling' => 15000],
        ];

        $counter = 1;
        foreach ($rawProducts as $item) {
            $cat = $categories->get($item['cat']);
            if (! $cat) {
                continue;
            }

            $sku = 'PRD-'.str_pad((string) $counter, 5, '0', STR_PAD_LEFT);
            $barcode = '899'.str_pad((string) $counter, 10, '0', STR_PAD_LEFT);

            $product = Product::firstOrCreate(
                ['sku' => $sku],
                [
                    'category_id' => $cat->id,
                    'sku' => $sku,
                    'barcode' => $barcode,
                    'name' => $item['name'],
                    'purchase_price' => $item['purchase'],
                    'selling_price' => $item['selling'],
                    'tax_rate' => 0,
                    'is_active' => true,
                ]
            );

            if ($outlet) {
                $stockQuantity = 50 + ($counter % 30);
                Stock::updateOrCreate(
                    [
                        'outlet_id' => $outlet->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'quantity' => $stockQuantity,
                    ]
                );

                StockMovement::firstOrCreate(
                    [
                        'outlet_id' => $outlet->id,
                        'product_id' => $product->id,
                        'type' => StockMovementType::Purchase,
                    ],
                    [
                        'reference_type' => 'INITIAL_SEED',
                        'reference_uuid' => (string) Str::uuid(),
                        'quantity' => $stockQuantity,
                        'notes' => 'Stok awal setup produk POS',
                    ]
                );
            }

            $counter++;
        }
    }
}
