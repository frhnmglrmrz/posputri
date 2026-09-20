<?php

namespace Tests\Feature;

use App\Enums\StockMovementType;
use App\Enums\TransactionStatus;
use App\Livewire\Products\ProductManager;
use App\Models\Category;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use Database\Seeders\AdminSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\OutletSeeder;
use Database\Seeders\PaymentMethodSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class OfflinePosSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic required data
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
     * Test bootstrap master data download for IndexedDB initialization (PRD Section 49).
     */
    public function test_sync_bootstrap_api_returns_complete_master_data(): void
    {
        $response = $this->getJson('/api/sync/bootstrap');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'outlet',
                    'categories',
                    'products',
                    'payment_methods',
                    'settings',
                    'customers',
                    'server_timestamp',
                ],
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertNotEmpty($response->json('data.products'));
        $this->assertNotEmpty($response->json('data.payment_methods'));
    }

    /**
     * Test incremental changes API returns records modified since timestamp (PRD Section 50).
     */
    public function test_sync_changes_api_returns_incremental_updates(): void
    {
        $since = now()->subHour()->toIso8601String();

        $response = $this->getJson('/api/sync/changes?since='.urlencode($since));

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'categories',
                    'products',
                    'server_timestamp',
                ],
            ]);
    }

    /**
     * Test sync transactions API records transaction and decrements inventory (PRD Section 33, 34).
     */
    public function test_sync_transactions_api_creates_records_and_decrements_inventory(): void
    {
        $outlet = Outlet::first();
        $cashier = User::where('email', 'kasir@posputri.test')->first();
        $product = Product::first();

        // Set known initial stock
        Stock::updateOrCreate(
            ['outlet_id' => $outlet->id, 'product_id' => $product->id],
            ['quantity' => 20]
        );

        $txUuid = (string) Str::uuid();
        $txNumber = 'TRX-'.strtoupper(Str::random(8));

        $payload = [
            'transactions' => [
                [
                    'uuid' => $txUuid,
                    'transaction_number' => $txNumber,
                    'cashier_id' => $cashier->id,
                    'outlet_id' => $outlet->id,
                    'subtotal' => 20000,
                    'discount' => 0,
                    'tax' => 0,
                    'total' => 20000,
                    'paid_amount' => 50000,
                    'change_amount' => 30000,
                    'payment_method' => 'cash',
                    'status' => 'completed',
                    'transaction_at' => now()->toIso8601String(),
                    'items' => [
                        [
                            'uuid' => (string) Str::uuid(),
                            'product_id' => $product->id,
                            'sku' => $product->sku,
                            'product_name' => $product->name,
                            'price' => 10000,
                            'quantity' => 2,
                            'discount' => 0,
                            'tax' => 0,
                            'subtotal' => 20000,
                        ],
                    ],
                    'payments' => [
                        [
                            'uuid' => (string) Str::uuid(),
                            'payment_method' => 'cash',
                            'amount' => 50000,
                            'status' => 'paid',
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/sync/transactions', $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'result' => [
                    'synced' => [
                        [
                            'uuid' => $txUuid,
                            'status' => 'SYNCED',
                        ],
                    ],
                    'failed' => [],
                ],
            ]);

        // Verify transaction saved in database
        $this->assertDatabaseHas('transactions', [
            'uuid' => $txUuid,
            'transaction_number' => $txNumber,
            'status' => TransactionStatus::Completed->value,
        ]);

        // Verify stock decremented by 2 (20 - 2 = 18)
        $currentStock = Stock::where('outlet_id', $outlet->id)->where('product_id', $product->id)->value('quantity');
        $this->assertEquals(18, $currentStock);

        // Verify stock movement recorded
        $this->assertDatabaseHas('stock_movements', [
            'outlet_id' => $outlet->id,
            'product_id' => $product->id,
            'quantity' => -2,
            'type' => StockMovementType::Sale->value,
            'reference_uuid' => $txUuid,
        ]);
    }

    /**
     * Test sync transactions API is idempotent (PRD Section 80: Duplicate Acceptance Test).
     */
    public function test_sync_transactions_is_idempotent_and_does_not_duplicate_stock_movements(): void
    {
        $outlet = Outlet::first();
        $cashier = User::where('email', 'kasir@posputri.test')->first();
        $product = Product::first();

        Stock::updateOrCreate(
            ['outlet_id' => $outlet->id, 'product_id' => $product->id],
            ['quantity' => 50]
        );

        $txUuid = (string) Str::uuid();
        $txNumber = 'TRX-IDEMPOTENT-001';

        $payload = [
            'transactions' => [
                [
                    'uuid' => $txUuid,
                    'transaction_number' => $txNumber,
                    'cashier_id' => $cashier->id,
                    'outlet_id' => $outlet->id,
                    'subtotal' => 15000,
                    'discount' => 0,
                    'tax' => 0,
                    'total' => 15000,
                    'paid_amount' => 20000,
                    'change_amount' => 5000,
                    'payment_method' => 'cash',
                    'status' => 'completed',
                    'transaction_at' => now()->toIso8601String(),
                    'items' => [
                        [
                            'uuid' => (string) Str::uuid(),
                            'product_id' => $product->id,
                            'sku' => $product->sku,
                            'product_name' => $product->name,
                            'price' => 15000,
                            'quantity' => 1,
                            'discount' => 0,
                            'tax' => 0,
                            'subtotal' => 15000,
                        ],
                    ],
                ],
            ],
        ];

        // Send 1st time
        $res1 = $this->postJson('/api/sync/transactions', $payload);
        $res1->assertOk()->assertJson(['success' => true]);

        // Send 2nd time with exact same UUID (simulating client retry)
        $res2 = $this->postJson('/api/sync/transactions', $payload);
        $res2->assertOk()->assertJson([
            'success' => true,
            'result' => [
                'synced' => [
                    [
                        'uuid' => $txUuid,
                        'status' => 'SYNCED',
                    ],
                ],
            ],
        ]);

        // Ensure only ONE transaction exists with this UUID
        $this->assertEquals(1, Transaction::where('uuid', $txUuid)->count());

        // Ensure stock decremented only ONCE (50 - 1 = 49)
        $stock = Stock::where('outlet_id', $outlet->id)->where('product_id', $product->id)->value('quantity');
        $this->assertEquals(49, $stock);
    }

    /**
     * Test stock conflict handling (PRD Rule 6: Stock conflict must not delete completed sales).
     */
    public function test_stock_conflict_handling_preserves_sale_even_when_insufficient_stock(): void
    {
        $outlet = Outlet::first();
        $cashier = User::where('email', 'kasir@posputri.test')->first();
        $product = Product::first();

        // Initial stock is low: only 2 available
        Stock::updateOrCreate(
            ['outlet_id' => $outlet->id, 'product_id' => $product->id],
            ['quantity' => 2]
        );

        $txUuid = (string) Str::uuid();

        $payload = [
            'transactions' => [
                [
                    'uuid' => $txUuid,
                    'transaction_number' => 'TRX-OVERSELL-001',
                    'cashier_id' => $cashier->id,
                    'outlet_id' => $outlet->id,
                    'subtotal' => 50000,
                    'discount' => 0,
                    'tax' => 0,
                    'total' => 50000,
                    'paid_amount' => 50000,
                    'change_amount' => 0,
                    'payment_method' => 'cash',
                    'status' => 'completed',
                    'transaction_at' => now()->toIso8601String(),
                    'items' => [
                        [
                            'uuid' => (string) Str::uuid(),
                            'product_id' => $product->id,
                            'sku' => $product->sku,
                            'product_name' => $product->name,
                            'price' => 10000,
                            'quantity' => 5, // Cashier sold 5 offline
                            'discount' => 0,
                            'tax' => 0,
                            'subtotal' => 50000,
                        ],
                    ],
                ],
            ],
        ];

        // The sync MUST succeed and not reject the completed cash sale
        $response = $this->postJson('/api/sync/transactions', $payload);
        $response->assertOk()->assertJson(['success' => true]);

        // Verify transaction is recorded
        $this->assertDatabaseHas('transactions', ['uuid' => $txUuid]);

        // Stock is reduced into negative (2 - 5 = -3) to reflect physical inventory deficit
        $finalStock = Stock::where('outlet_id', $outlet->id)->where('product_id', $product->id)->value('quantity');
        $this->assertEquals(-3, $finalStock);
    }

    /**
     * Test shift synchronization and cash difference reconciliation (PRD Section 42, 43).
     */
    public function test_sync_shift_records_reconciliation_correctly(): void
    {
        $outlet = Outlet::first();
        $cashier = User::where('email', 'kasir@posputri.test')->first();
        $shiftUuid = (string) Str::uuid();

        $payload = [
            'uuid' => $shiftUuid,
            'outlet_id' => $outlet->id,
            'cashier_id' => $cashier->id,
            'opening_cash' => 200000,
            'expected_cash' => 450000,
            'closing_cash' => 445000,
            'difference' => -5000,
            'status' => 'closed',
            'opened_at' => now()->subHours(8)->toIso8601String(),
            'closed_at' => now()->toIso8601String(),
            'notes' => 'Kasir tutup shift normal dengan selisih 5000',
        ];

        $response = $this->postJson('/api/sync/shifts', $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'shift' => [
                    'uuid' => $shiftUuid,
                    'difference' => -5000,
                    'status' => 'closed',
                ],
            ]);

        $this->assertDatabaseHas('shifts', [
            'uuid' => $shiftUuid,
            'difference' => -5000,
            'status' => 'closed',
        ]);
    }

    /**
     * Test supervisor void transaction restores inventory (PRD Section 15).
     */
    public function test_void_transaction_reverses_inventory(): void
    {
        $outlet = Outlet::first();
        $cashier = User::where('email', 'kasir@posputri.test')->first();
        $product = Product::first();

        Stock::updateOrCreate(
            ['outlet_id' => $outlet->id, 'product_id' => $product->id],
            ['quantity' => 10]
        );

        $service = app(TransactionService::class);

        // Create transaction of 3 items (stock becomes 10 - 3 = 7)
        $tx = $service->syncTransaction([
            'uuid' => (string) Str::uuid(),
            'transaction_number' => 'TRX-VOID-TEST-001',
            'cashier_id' => $cashier->id,
            'outlet_id' => $outlet->id,
            'subtotal' => 30000,
            'total' => 30000,
            'paid_amount' => 30000,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'status' => 'completed',
            'items' => [
                [
                    'uuid' => (string) Str::uuid(),
                    'product_id' => $product->id,
                    'price' => 10000,
                    'quantity' => 3,
                    'subtotal' => 30000,
                ],
            ],
        ]);

        $stockAfterSale = Stock::where('outlet_id', $outlet->id)->where('product_id', $product->id)->value('quantity');
        $this->assertEquals(7, $stockAfterSale);

        // Void the transaction
        $voided = $service->voidTransaction($tx, 'Customer canceled order');
        $this->assertTrue($voided);

        // Check status changed to void
        $this->assertEquals(TransactionStatus::Void, $tx->fresh()->status);

        // Check stock restored back to 10 (7 + 3 = 10)
        $stockAfterVoid = Stock::where('outlet_id', $outlet->id)->where('product_id', $product->id)->value('quantity');
        $this->assertEquals(10, $stockAfterVoid);

        // Check return stock movement exists
        $this->assertDatabaseHas('stock_movements', [
            'outlet_id' => $outlet->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'type' => StockMovementType::ReturnOrder->value,
            'reference_uuid' => $tx->uuid,
        ]);
    }

    /**
     * Test admin can access all back-office Livewire views.
     */
    public function test_admin_can_access_all_management_routes(): void
    {
        $admin = User::where('email', 'admin@posputri.test')->first();

        $routes = [
            'products.index',
            'categories.index',
            'inventory.index',
            'transactions.index',
            'shifts.index',
            'customers.index',
            'reports.index',
            'sync.index',
            'users.index',
            'settings.index',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($admin)->get(route($route));
            $response->assertOk();
        }
    }

    /**
     * Test cashier is restricted from admin/supervisor routes but can access cashier routes.
     */
    public function test_cashier_permissions_and_route_restrictions(): void
    {
        $cashier = User::where('email', 'kasir@posputri.test')->first();

        // Allowed routes
        $this->actingAs($cashier)->get(route('pos.index'))->assertOk();
        $this->actingAs($cashier)->get(route('shifts.index'))->assertOk();
        $this->actingAs($cashier)->get(route('transactions.index'))->assertOk();

        // Restricted admin/supervisor routes should return 403 Forbidden
        $this->actingAs($cashier)->get(route('users.index'))->assertForbidden();
        $this->actingAs($cashier)->get(route('settings.index'))->assertForbidden();
        $this->actingAs($cashier)->get(route('inventory.index'))->assertForbidden();
        $this->actingAs($cashier)->get(route('reports.index'))->assertForbidden();
    }

    /**
     * Test dashboard renders personalized view for each user role.
     */
    public function test_dashboard_renders_personalized_view_for_each_user_role(): void
    {
        $admin = User::where('email', 'admin@posputri.test')->first();
        $supervisor = User::where('email', 'supervisor@posputri.test')->first();
        $cashier = User::where('email', 'kasir@posputri.test')->first();

        // 1. Admin Dashboard
        $adminRes = $this->actingAs($admin)->get(route('dashboard'));
        $adminRes->assertOk()
            ->assertSee('Selamat Datang, Administrator')
            ->assertSee('Omzet Bulan Ini')
            ->assertSee('Valuasi Aset Stok')
            ->assertSee('Pintasan Administrator');

        // 2. Supervisor Dashboard
        $spvRes = $this->actingAs($supervisor)->get(route('dashboard'));
        $spvRes->assertOk()
            ->assertSee('Selamat Datang, Supervisor Toko')
            ->assertSee('Omzet Outlet Hari Ini')
            ->assertSee('Rata-rata Basket')
            ->assertSee('Peringatan Stok Kritis');

        // 3. Cashier Dashboard
        $cashierRes = $this->actingAs($cashier)->get(route('dashboard'));
        $cashierRes->assertOk()
            ->assertSee('Selamat Datang, '.$cashier->name)
            ->assertSee('Status Shift Anda')
            ->assertSee('Penjualan Saya Hari Ini')
            ->assertSee('BUKA KASIR SEKARANG');
    }

    /**
     * Test products can be searched by SKU via livewire product manager.
     */
    public function test_products_can_be_queried_by_sku(): void
    {
        $product = Product::first();
        $this->assertNotNull($product);

        // 1. Direct model query by SKU
        $found = Product::where('sku', $product->sku)->first();
        $this->assertEquals($product->id, $found->id);

        // 2. Search by SKU via Staff Inventory livewire manager
        $inventory = User::where('email', 'gudang@berkahmart.test')->first();
        Livewire::actingAs($inventory)
            ->test(ProductManager::class)
            ->set('search', $product->sku)
            ->assertSee($product->name)
            ->assertSee($product->sku);
    }

    /**
     * Test SKU and Transaction number are auto-generated properly.
     */
    public function test_sku_and_transaction_numbers_are_auto_generated(): void
    {
        // 1. Next SKU generates correctly with PRD- prefix and padded counter
        $nextSku = Product::generateNextSku();
        $this->assertMatchesRegularExpression('/^PRD-\d{5}$/', $nextSku);

        // 2. Product created without SKU automatically gets generated SKU
        $category = Category::first();
        $createdProduct = Product::create([
            'category_id' => $category->id,
            'name' => 'Auto SKU Test Product',
            'purchase_price' => 1000,
            'selling_price' => 2000,
        ]);
        $this->assertNotEmpty($createdProduct->sku);
        $this->assertMatchesRegularExpression('/^PRD-\d{5}$/', $createdProduct->sku);

        // 3. Livewire ProductManager modal initializes with auto SKU and can regenerate
        $inventory = User::where('email', 'gudang@berkahmart.test')->first();
        Livewire::actingAs($inventory)
            ->test(ProductManager::class)
            ->call('openCreateModal')
            ->assertSet('isModalOpen', true)
            ->assertSet('sku', Product::generateNextSku())
            ->call('regenerateSku')
            ->assertSet('isModalOpen', true);

        // 4. Transaction number auto-generation format TRX-YYYYMMDD-XXXX
        $nextTrxNumber = Transaction::generateNextNumber();
        $this->assertMatchesRegularExpression('/^TRX-\d{8}-[A-Z0-9]{4}$/', $nextTrxNumber);

        // 5. Transaction created via TransactionService without transaction_number gets auto TRX number
        $outlet = Outlet::first();
        $cashier = User::where('email', 'kasir@posputri.test')->first();
        $service = app(TransactionService::class);
        $trx = $service->syncTransaction([
            'outlet_id' => $outlet->id,
            'cashier_id' => $cashier->id,
            'subtotal' => 5000,
            'total' => 5000,
            'items' => [
                [
                    'product_id' => $createdProduct->id,
                    'quantity' => 1,
                    'price' => 2000,
                ],
            ],
        ]);

        $this->assertNotEmpty($trx->transaction_number);
        $this->assertMatchesRegularExpression('/^TRX-\d{8}-[A-Z0-9]{4}$/', $trx->transaction_number);
    }
}
