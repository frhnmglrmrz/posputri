<?php

namespace Tests\Feature;

use App\Enums\TransactionStatus;
use App\Livewire\Products\ProductManager;
use App\Models\Category;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Shift;
use App\Models\User;
use App\Services\SyncService;
use App\Services\TransactionService;
use Database\Seeders\AdminSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\OutletSeeder;
use Database\Seeders\PaymentMethodSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProductImageAndCashierFlowTest extends TestCase
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

        Storage::fake('public');
    }

    /**
     * Test product image upload, storage, image_url accessor, and image removal.
     */
    public function test_product_image_upload_and_removal(): void
    {
        $admin = User::where('email', 'admin@berkahmart.test')->first();
        $category = Category::first();

        $file = UploadedFile::fake()->image('matcha.jpg', 400, 400);

        // 1. Create product with image upload
        Livewire::actingAs($admin)
            ->test(ProductManager::class)
            ->call('openCreateModal')
            ->set('category_id', $category->id)
            ->set('name', 'Matcha Latte Spesial Baru')
            ->set('barcode', '89999990001')
            ->set('purchase_price', 12000)
            ->set('selling_price', 18000)
            ->set('imageFile', $file)
            ->call('save')
            ->assertHasNoErrors();

        $product = Product::where('name', 'Matcha Latte Spesial Baru')->first();
        $this->assertNotNull($product);
        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);
        $this->assertStringContainsString('storage/'.$product->image, $product->image_url);

        // 2. Edit product and remove image
        Livewire::actingAs($admin)
            ->test(ProductManager::class)
            ->call('edit', $product->id)
            ->call('removeCurrentImage')
            ->call('save')
            ->assertHasNoErrors();

        $product->refresh();
        $this->assertNull($product->image);
        $this->assertStringContainsString('images/product-placeholder.svg', $product->image_url);
    }

    /**
     * Test SyncService includes product image and image_url for offline POS IndexedDB caching.
     */
    public function test_sync_service_includes_product_images(): void
    {
        $outlet = Outlet::first();
        $syncService = app(SyncService::class);

        $bootstrapData = $syncService->getBootstrapData($outlet);
        $this->assertNotEmpty($bootstrapData['products']);
        $firstProduct = $bootstrapData['products'][0];

        $this->assertArrayHasKey('image', $firstProduct);
        $this->assertArrayHasKey('image_url', $firstProduct);

        $changes = $syncService->getChanges($outlet);
        $firstChange = $changes['products'][0];
        $this->assertArrayHasKey('image', $firstChange);
        $this->assertArrayHasKey('image_url', $firstChange);
    }

    /**
     * Test Cashier can execute transactions directly without an active shift.
     */
    public function test_cashier_can_execute_sales_transaction_without_active_shift(): void
    {
        $cashier = User::where('email', 'kasir@berkahmart.test')->first();
        $outlet = Outlet::first();
        $product = Product::first();

        // Ensure cashier has no open shifts
        $this->assertEquals(0, Shift::where('cashier_id', $cashier->id)->where('status', 'open')->count());

        $txService = app(TransactionService::class);

        $tx = $txService->syncTransaction([
            'outlet_id' => $outlet->id,
            'cashier_id' => $cashier->id,
            'shift_id' => null, // Explicitly no shift
            'subtotal' => (float) $product->selling_price,
            'total' => (float) $product->selling_price,
            'paid_amount' => (float) $product->selling_price,
            'change_amount' => 0,
            'items' => [
                [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => (float) $product->selling_price,
                    'quantity' => 1,
                    'subtotal' => (float) $product->selling_price,
                ],
            ],
            'payments' => [
                [
                    'payment_method' => 'cash',
                    'amount' => (float) $product->selling_price,
                ],
            ],
        ]);

        $this->assertNotNull($tx);
        $this->assertNull($tx->shift_id);
        $this->assertEquals(TransactionStatus::Completed, $tx->status);

        // Verify Cashier Dashboard loads cleanly without requiring shift
        $response = $this->actingAs($cashier)->get(route('dashboard'));
        $response->assertOk()
            ->assertSee('SIAP TRANSAKSI')
            ->assertSee('Mulai Penjualan Langsung')
            ->assertDontSee('BELUM BUKA SHIFT')
            ->assertDontSee('Buka Shift Sekarang');
    }

    /**
     * Test historical shifts table and supervisor access are preserved for audit.
     */
    public function test_historical_shifts_preserved_and_accessible_to_supervisor(): void
    {
        $supervisor = User::where('email', 'supervisor@berkahmart.test')->first();
        $cashier = User::where('email', 'kasir@berkahmart.test')->first();
        $outlet = Outlet::first();

        // Create a historical shift record
        $shift = Shift::create([
            'outlet_id' => $outlet->id,
            'cashier_id' => $cashier->id,
            'opening_cash' => 200000,
            'expected_cash' => 500000,
            'closing_cash' => 500000,
            'status' => 'closed',
            'opened_at' => now()->subDay(),
            'closed_at' => now()->subDay()->addHours(8),
        ]);

        $this->assertDatabaseHas('shifts', ['id' => $shift->id]);

        // Supervisor can view shifts audit page
        $this->actingAs($supervisor)->get(route('shifts.index'))->assertOk();

        // Cashier cannot access shifts page
        $this->actingAs($cashier)->get(route('shifts.index'))->assertForbidden();
    }
}
