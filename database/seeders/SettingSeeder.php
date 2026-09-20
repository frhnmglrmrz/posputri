<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'store_name', 'value' => 'Berkah Mart Outlet 01', 'group' => 'store'],
            ['key' => 'store_address', 'value' => 'Jl. Boulevard Raya No. 1, Kelapa Gading, Jakarta', 'group' => 'store'],
            ['key' => 'store_phone', 'value' => '021-5551234', 'group' => 'store'],
            ['key' => 'tax_percentage', 'value' => '11', 'group' => 'financial'],
            ['key' => 'currency_symbol', 'value' => 'Rp', 'group' => 'financial'],
            ['key' => 'receipt_header', 'value' => 'BERKAH MART RETAIL & GROCERY', 'group' => 'receipt'],
            ['key' => 'receipt_footer', 'value' => 'Terima kasih atas kunjungan Anda di Berkah Mart. Simpan struk ini sebagai bukti pembayaran sah.', 'group' => 'receipt'],
            ['key' => 'offline_mode_enabled', 'value' => '1', 'group' => 'system'],
            ['key' => 'auto_sync_interval', 'value' => '60', 'group' => 'system'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group']]
            );
        }
    }
}
