<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Livewire\Categories\CategoryManager;
use App\Livewire\Customers\CustomerManager;
use App\Livewire\Inventory\InventoryManager;
use App\Livewire\Outlets\OutletManager;
use App\Livewire\Products\ProductManager;
use App\Livewire\Reports\ReportManager;
use App\Livewire\Shifts\ShiftManager;
use App\Livewire\Sync\SyncCenter;
use App\Livewire\Transactions\TransactionHistory;
use App\Livewire\Users\UserManager;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // POS Kasir (Offline-First Alpine + Dexie)
    Route::middleware('role:Admin|Supervisor|Cashier')->get('/pos', function () {
        return view('pos.index');
    })->name('pos.index');

    // Operational routes (Cashier, Supervisor, Admin)
    Route::middleware('role:Admin|Supervisor|Cashier')->group(function () {
        Route::get('/shifts', ShiftManager::class)->name('shifts.index');
        Route::get('/transactions', TransactionHistory::class)->name('transactions.index');
        Route::get('/customers', CustomerManager::class)->name('customers.index');
    });

    // Inventory, Supervisor & Admin routes (Input Barang, Kategori & Stok Opname)
    Route::middleware('role:Admin|Supervisor|Inventory')->group(function () {
        Route::get('/products', ProductManager::class)->name('products.index');
        Route::get('/categories', CategoryManager::class)->name('categories.index');
        Route::get('/inventory', InventoryManager::class)->name('inventory.index');
    });

    // Supervisor & Admin routes (Laporan & Sinkronisasi)
    Route::middleware('role:Admin|Supervisor')->group(function () {
        Route::get('/reports', ReportManager::class)->name('reports.index');
        Route::get('/sync', SyncCenter::class)->name('sync.index');
    });

    // Admin only routes
    Route::middleware('role:Admin')->group(function () {
        Route::get('/users', UserManager::class)->name('users.index');
        Route::get('/settings', OutletManager::class)->name('settings.index');
    });
});
