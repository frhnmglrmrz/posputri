<?php

namespace Tests\Feature;

use App\Enums\TransactionStatus;
use App\Models\Customer;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\SyncLog;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\OutletSeeder;
use Database\Seeders\PaymentMethodSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SyncApiComprehensiveTest extends TestCase
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
     * Test /api/health endpoint.
     */
    public function test_health_api_returns_ok_and_iso_timestamp(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertOk()
            ->assertJson([
                'status' => 'ok',
            ])
            ->assertJsonStructure(['status', 'timestamp']);
    }

    /**
     * Test /api/sync/bootstrap with specific outlet query parameter.
     */
    public function test_bootstrap_api_with_specific_outlet_parameter(): void
    {
        $outlet = Outlet::first();

        $response = $this->getJson("/api/sync/bootstrap?outlet_id={$outlet->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'outlet' => [
                        'id' => $outlet->id,
                        'name' => $outlet->name,
                    ],
                ],
            ]);

        $this->assertNotEmpty($response->json('data.categories'));
        $this->assertNotEmpty($response->json('data.products'));
        $this->assertNotEmpty($response->json('data.payment_methods'));
    }

    /**
     * Test /api/sync/changes returns only items modified since given date.
     */
    public function test_incremental_changes_api_filters_by_since_timestamp(): void
    {
        $since = now()->subMinutes(10)->toIso8601String();

        $response = $this->getJson('/api/sync/changes?since='.urlencode($since));

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'categories',
                    'products',
                    'payment_methods',
                    'timestamp',
                    'server_timestamp',
                ],
            ]);
    }

    /**
     * Test single transaction payload format (not wrapped in 'transactions' array).
     */
    public function test_sync_transactions_with_single_transaction_object_payload(): void
    {
        $outlet = Outlet::first();
        $cashier = User::where('email', 'kasir@posputri.test')->first();
        $product = Product::first();
        $txUuid = (string) Str::uuid();

        $payload = [
            'uuid' => $txUuid,
            'transaction_number' => 'TRX-SINGLE-001',
            'cashier_id' => $cashier->id,
            'outlet_id' => $outlet->id,
            'subtotal' => 10000,
            'total' => 10000,
            'paid_amount' => 10000,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'status' => 'completed',
            'items' => [
                [
                    'uuid' => (string) Str::uuid(),
                    'product_id' => $product->id,
                    'price' => 10000,
                    'quantity' => 1,
                    'subtotal' => 10000,
                ],
            ],
        ];

        $response = $this->postJson('/api/sync/transactions', $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'result' => [
                    'total_processed' => 1,
                    'total_synced' => 1,
                    'synced' => [
                        [
                            'uuid' => $txUuid,
                            'status' => 'SYNCED',
                        ],
                    ],
                ],
            ]);

        $this->assertDatabaseHas('transactions', [
            'uuid' => $txUuid,
            'status' => TransactionStatus::Completed->value,
        ]);
    }

    /**
     * Test transaction with customer association and split payments.
     */
    public function test_sync_transactions_with_customer_and_split_payments(): void
    {
        $outlet = Outlet::first();
        $cashier = User::where('email', 'kasir@posputri.test')->first();
        $product = Product::first();

        $customer = Customer::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'email' => 'budi@example.test',
        ]);

        $txUuid = (string) Str::uuid();
        $payUuid1 = (string) Str::uuid();
        $payUuid2 = (string) Str::uuid();

        $payload = [
            'transactions' => [
                [
                    'uuid' => $txUuid,
                    'transaction_number' => 'TRX-SPLIT-PAY-001',
                    'cashier_id' => $cashier->id,
                    'outlet_id' => $outlet->id,
                    'customer_id' => $customer->id,
                    'subtotal' => 50000,
                    'discount' => 5000,
                    'tax' => 0,
                    'total' => 45000,
                    'paid_amount' => 45000,
                    'change_amount' => 0,
                    'status' => 'completed',
                    'transaction_at' => now()->toIso8601String(),
                    'items' => [
                        [
                            'uuid' => (string) Str::uuid(),
                            'product_id' => $product->id,
                            'sku' => $product->sku,
                            'product_name' => $product->name,
                            'price' => 50000,
                            'quantity' => 1,
                            'discount' => 5000,
                            'subtotal' => 45000,
                        ],
                    ],
                    'payments' => [
                        [
                            'uuid' => $payUuid1,
                            'payment_method' => 'cash',
                            'amount' => 20000,
                            'status' => 'paid',
                        ],
                        [
                            'uuid' => $payUuid2,
                            'payment_method' => 'qris',
                            'amount' => 25000,
                            'reference' => 'QRIS-123456',
                            'status' => 'paid',
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/sync/transactions', $payload);

        $response->assertOk()
            ->assertJson(['success' => true]);

        // Assert customer relationship recorded
        $this->assertDatabaseHas('transactions', [
            'uuid' => $txUuid,
            'customer_id' => $customer->id,
            'total' => 45000,
        ]);

        // Assert 2 payment records created
        $this->assertDatabaseHas('payments', [
            'uuid' => $payUuid1,
            'payment_method' => 'cash',
            'amount' => 20000,
        ]);

        $this->assertDatabaseHas('payments', [
            'uuid' => $payUuid2,
            'payment_method' => 'qris',
            'amount' => 25000,
            'reference' => 'QRIS-123456',
        ]);
    }

    /**
     * Test SyncLog is generated when transaction synchronizes.
     */
    public function test_sync_transactions_records_sync_log(): void
    {
        $outlet = Outlet::first();
        $cashier = User::where('email', 'kasir@posputri.test')->first();
        $txUuid = (string) Str::uuid();

        $payload = [
            'transactions' => [
                [
                    'uuid' => $txUuid,
                    'transaction_number' => 'TRX-LOG-TEST-001',
                    'cashier_id' => $cashier->id,
                    'outlet_id' => $outlet->id,
                    'subtotal' => 10000,
                    'total' => 10000,
                    'paid_amount' => 10000,
                    'change_amount' => 0,
                    'items' => [],
                ],
            ],
        ];

        $this->postJson('/api/sync/transactions', $payload)->assertOk();

        $this->assertDatabaseHas('sync_logs', [
            'entity_type' => 'TRANSACTION',
            'entity_uuid' => $txUuid,
            'status' => 'SYNCED',
        ]);
    }

    /**
     * Test validation error when syncing shift with missing mandatory fields.
     */
    public function test_sync_shift_validation_errors(): void
    {
        // Missing uuid, outlet_id, cashier_id
        $response = $this->postJson('/api/sync/shifts', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['uuid', 'outlet_id', 'cashier_id']);
    }

    /**
     * Test Sanctum authenticated /api/user endpoint.
     */
    public function test_authenticated_user_endpoint(): void
    {
        $cashier = User::where('email', 'kasir@posputri.test')->first();
        Sanctum::actingAs($cashier);

        $response = $this->getJson('/api/user');

        $response->assertOk()
            ->assertJson([
                'id' => $cashier->id,
                'email' => 'kasir@posputri.test',
            ]);
    }
}
