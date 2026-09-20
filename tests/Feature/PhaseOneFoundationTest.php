<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseOneFoundationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test health check endpoint returns 200 and ok status.
     */
    public function test_health_endpoint_returns_ok_status(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertOk()
            ->assertJson(['status' => 'ok']);
    }

    /**
     * Test root route displays the welcome page for guests.
     */
    public function test_guest_can_view_welcome_page_from_root(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('POS PUTRI')
            ->assertSee('Terpercaya (Reliable)')
            ->assertSee('Berani (Bold)')
            ->assertSee('Transparan (Transparent)');
    }

    /**
     * Test guest can view the login page.
     */
    public function test_guest_can_view_login_page(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk()
            ->assertSee('POS Putri Offline-First')
            ->assertSee('admin@posputri.test');
    }

    /**
     * Test user authentication and redirection to dashboard for Admin.
     */
    public function test_admin_can_login_and_is_redirected_to_dashboard(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->post(route('login'), [
            'email' => 'admin@posputri.test',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    /**
     * Test cashier is redirected to POS upon login.
     */
    public function test_cashier_can_login_and_is_redirected_to_pos(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->post(route('login'), [
            'email' => 'kasir@posputri.test',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('pos.index'));
    }

    /**
     * Test invalid password is rejected.
     */
    public function test_user_cannot_login_with_invalid_password(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->post(route('login'), [
            'email' => 'admin@posputri.test',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test authenticated user can logout.
     */
    public function test_authenticated_user_can_logout(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::where('email', 'admin@posputri.test')->first();

        $response = $this->actingAs($user)->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    /**
     * Test all seeders populate master data and >= 100 products.
     */
    public function test_database_seeders_populate_required_data(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('outlets', ['code' => 'OUT-001']);
        $this->assertDatabaseCount('devices', 2);
        $this->assertDatabaseCount('categories', 6);
        $this->assertDatabaseCount('payment_methods', 6);
        $this->assertTrue(Product::count() >= 100);
        $this->assertDatabaseCount('stocks', Product::count());
        $this->assertDatabaseCount('stock_movements', Product::count());
    }

    /**
     * Test roles and permissions are properly set up.
     */
    public function test_roles_and_permissions_are_configured(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@posputri.test')->first();
        $supervisor = User::where('email', 'supervisor@posputri.test')->first();
        $cashier = User::where('email', 'kasir@posputri.test')->first();
        $inventory = User::where('email', 'gudang@posputri.test')->first();

        $this->assertTrue($admin->hasRole('Admin'));
        $this->assertTrue($supervisor->hasRole('Supervisor'));
        $this->assertTrue($cashier->hasRole('Cashier'));
        $this->assertTrue($inventory->hasRole('Inventory'));

        $this->assertTrue($cashier->hasPermissionTo('access-pos'));
        $this->assertTrue($cashier->hasPermissionTo('checkout'));
        $this->assertFalse($cashier->hasPermissionTo('manage-users'));

        $this->assertTrue($inventory->hasPermissionTo('manage-products'));
        $this->assertTrue($inventory->hasPermissionTo('manage-categories'));
        $this->assertTrue($inventory->hasPermissionTo('manage-inventory'));
        $this->assertTrue($inventory->hasPermissionTo('adjust-stock'));
        $this->assertFalse($inventory->hasPermissionTo('access-pos'));
        $this->assertFalse($inventory->hasPermissionTo('manage-users'));

        $this->assertTrue($admin->hasPermissionTo('manage-users'));
        $this->assertTrue($admin->hasPermissionTo('access-pos'));
    }

    /**
     * Test inventory staff can access catalog and dashboard, but not POS, shifts, or admin routes.
     */
    public function test_inventory_staff_access_permissions(): void
    {
        $this->seed(DatabaseSeeder::class);
        $inventory = User::where('email', 'gudang@posputri.test')->first();

        // Can access dashboard with inventory view
        $dashboardResponse = $this->actingAs($inventory)->get(route('dashboard'));
        $dashboardResponse->assertOk()
            ->assertSee('Staff Gudang')
            ->assertSee('Total Katalog Produk');

        // Can access products, categories, inventory
        $this->actingAs($inventory)->get(route('products.index'))->assertOk();
        $this->actingAs($inventory)->get(route('categories.index'))->assertOk();
        $this->actingAs($inventory)->get(route('inventory.index'))->assertOk();

        // Cannot access POS or Cashier Shifts or Admin routes
        $this->actingAs($inventory)->get(route('pos.index'))->assertForbidden();
        $this->actingAs($inventory)->get(route('shifts.index'))->assertForbidden();
        $this->actingAs($inventory)->get(route('reports.index'))->assertForbidden();
        $this->actingAs($inventory)->get(route('users.index'))->assertForbidden();
    }

    /**
     * Test models automatically generate UUID upon creation.
     */
    public function test_models_automatically_generate_uuid(): void
    {
        $outlet = Outlet::create([
            'name' => 'Test Outlet',
            'code' => 'TEST-01',
            'is_active' => true,
        ]);

        $this->assertNotNull($outlet->uuid);
        $this->assertEquals(36, strlen($outlet->uuid));

        $category = Category::create([
            'name' => 'Minuman',
            'slug' => 'minuman-test',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'TEST-SKU-1',
            'name' => 'Kopi Test',
            'selling_price' => 15000,
            'is_active' => true,
        ]);

        $this->assertNotNull($product->uuid);
        $this->assertEquals(36, strlen($product->uuid));
    }

    /**
     * Test dashboard view renders for authenticated user.
     */
    public function test_authenticated_user_can_access_dashboard_and_pos(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::where('email', 'admin@posputri.test')->first();

        $dashboardResponse = $this->actingAs($user)->get(route('dashboard'));
        $dashboardResponse->assertOk()
            ->assertSee('Selamat Datang, Administrator')
            ->assertSee('Outlet Utama');

        $posResponse = $this->actingAs($user)->get(route('pos.index'));
        $posResponse->assertOk()
            ->assertSee('Cari produk berdasarkan nama');
    }
}
