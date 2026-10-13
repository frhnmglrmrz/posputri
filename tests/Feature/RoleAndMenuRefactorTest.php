<?php

namespace Tests\Feature;

use App\Enums\TransactionStatus;
use App\Livewire\Inventory\InventoryManager;
use App\Livewire\Products\ProductManager;
use App\Livewire\Reports\ReportManager;
use App\Livewire\Transactions\TransactionHistory;
use App\Models\Outlet;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\OutletSeeder;
use Database\Seeders\PaymentMethodSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RoleAndMenuRefactorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            OutletSeeder::class,
            AdminSeeder::class,
            PaymentMethodSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }

    /**
     * Test Owner can access dashboard, reports, transactions, and customers, but is blocked from operational actions.
     */
    public function test_owner_role_access_and_strict_operational_restrictions(): void
    {
        $owner = User::where('email', 'owner@berkahmart.test')->first();
        $this->assertNotNull($owner);
        $this->assertTrue($owner->hasRole('Owner'));

        // 1. Allowed Read-Only Routes
        $dashboardRes = $this->actingAs($owner)->get(route('dashboard'));
        $dashboardRes->assertOk()
            ->assertSee('Ringkasan Analisis Performa Bisnis')
            ->assertSee('Lihat Laporan Penjualan')
            ->assertDontSee('Buka Terminal Kasir');

        $this->actingAs($owner)->get(route('reports.index'))->assertOk();
        $this->actingAs($owner)->get(route('transactions.index'))->assertOk();
        $this->actingAs($owner)->get(route('customers.index'))->assertOk();

        // 2. Forbidden Operational & Administrative Routes
        $this->actingAs($owner)->get(route('pos.index'))->assertForbidden();
        $this->actingAs($owner)->get(route('shifts.index'))->assertForbidden();
        $this->actingAs($owner)->get(route('products.index'))->assertForbidden();
        $this->actingAs($owner)->get(route('categories.index'))->assertForbidden();
        $this->actingAs($owner)->get(route('inventory.index'))->assertForbidden();
        $this->actingAs($owner)->get(route('users.index'))->assertForbidden();
        $this->actingAs($owner)->get(route('settings.index'))->assertForbidden();
        $this->actingAs($owner)->get(route('sync.index'))->assertForbidden();
    }

    /**
     * Test Owner cannot void transactions.
     */
    public function test_owner_cannot_void_transactions(): void
    {
        $owner = User::where('email', 'owner@berkahmart.test')->first();
        $outlet = Outlet::first();
        $cashier = User::where('email', 'kasir@berkahmart.test')->first();

        $tx = Transaction::create([
            'outlet_id' => $outlet->id,
            'cashier_id' => $cashier->id,
            'transaction_number' => 'TRX-TEST-001',
            'subtotal' => 50000,
            'total' => 50000,
            'paid_amount' => 50000,
            'change_amount' => 0,
            'status' => TransactionStatus::Completed,
            'transaction_at' => now(),
        ]);

        $this->actingAs($owner);

        Livewire::test(TransactionHistory::class)
            ->call('voidTransaction', $tx->id)
            ->assertSee('Hanya Admin atau Supervisor yang berhak membatalkan transaksi.');

        $this->assertEquals(TransactionStatus::Completed, $tx->fresh()->status);
    }

    /**
     * Test Admin role retains access to system administration and reports.
     */
    public function test_admin_role_access(): void
    {
        $admin = User::where('email', 'admin@berkahmart.test')->first();
        $this->assertTrue($admin->hasRole('Admin'));

        $this->actingAs($admin)->get(route('dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('users.index'))->assertOk();
        $this->actingAs($admin)->get(route('settings.index'))->assertOk();
        $this->actingAs($admin)->get(route('sync.index'))->assertOk();
        $this->actingAs($admin)->get(route('products.index'))->assertOk();
        $this->actingAs($admin)->get(route('reports.index'))->assertOk();
    }

    /**
     * Test Cashier role cannot access administrative or inventory or report routes.
     */
    public function test_cashier_role_restrictions(): void
    {
        $cashier = User::where('email', 'kasir@berkahmart.test')->first();
        $this->assertTrue($cashier->hasRole('Cashier'));

        $this->actingAs($cashier)->get(route('pos.index'))->assertOk();
        $this->actingAs($cashier)->get(route('shifts.index'))->assertOk();

        $this->actingAs($cashier)->get(route('reports.index'))->assertForbidden();
        $this->actingAs($cashier)->get(route('inventory.index'))->assertForbidden();
        $this->actingAs($cashier)->get(route('products.index'))->assertForbidden();
        $this->actingAs($cashier)->get(route('users.index'))->assertForbidden();
        $this->actingAs($cashier)->get(route('settings.index'))->assertForbidden();
    }

    /**
     * Test Dashboard analytics calculation with real database data and periods.
     */
    public function test_dashboard_analytics_with_actual_data_and_periods(): void
    {
        $outlet = Outlet::first();
        $cashier = User::where('email', 'kasir@berkahmart.test')->first();
        $product = Product::first();

        // Create a completed transaction today
        $tx = Transaction::create([
            'outlet_id' => $outlet->id,
            'cashier_id' => $cashier->id,
            'transaction_number' => 'TRX-REAL-01',
            'subtotal' => 100000,
            'total' => 100000,
            'paid_amount' => 100000,
            'change_amount' => 0,
            'status' => TransactionStatus::Completed,
            'transaction_at' => now(),
        ]);

        TransactionItem::create([
            'transaction_id' => $tx->id,
            'product_id' => $product->id,
            'sku' => $product->sku,
            'product_name' => $product->name,
            'price' => 50000,
            'quantity' => 2,
            'subtotal' => 100000,
        ]);

        Payment::create([
            'transaction_id' => $tx->id,
            'payment_method' => 'cash',
            'amount' => 100000,
            'status' => 'paid',
        ]);

        $owner = User::where('email', 'owner@berkahmart.test')->first();

        $responseToday = $this->actingAs($owner)->get(route('dashboard', ['period' => 'today']));
        $responseToday->assertOk()
            ->assertSee('Rp100.000')
            ->assertSee($product->name);

        $response7Days = $this->actingAs($owner)->get(route('dashboard', ['period' => '7_days']));
        $response7Days->assertOk()
            ->assertSee('Rp100.000');
    }

    /**
     * Test ReportManager CSV export works and streams valid content.
     */
    public function test_report_manager_csv_export(): void
    {
        $owner = User::where('email', 'owner@berkahmart.test')->first();
        $this->actingAs($owner);

        Livewire::test(ReportManager::class)
            ->call('exportCsv')
            ->assertFileDownloaded();
    }

    /**
     * Test ProductManager searches by barcode.
     */
    public function test_product_manager_searches_by_barcode(): void
    {
        $admin = User::where('email', 'admin@berkahmart.test')->first();
        $this->actingAs($admin);

        $product = Product::whereNotNull('barcode')->first();
        $this->assertNotNull($product);

        Livewire::test(ProductManager::class)
            ->set('search', $product->barcode)
            ->assertSee($product->name)
            ->assertSee($product->barcode);
    }

    /**
     * Test InventoryManager searches by barcode.
     */
    public function test_inventory_manager_searches_by_barcode(): void
    {
        $admin = User::where('email', 'admin@berkahmart.test')->first();
        $this->actingAs($admin);

        $product = Product::whereNotNull('barcode')->first();
        $this->assertNotNull($product);

        Livewire::test(InventoryManager::class)
            ->set('search', $product->barcode)
            ->assertSee($product->name);
    }
}
