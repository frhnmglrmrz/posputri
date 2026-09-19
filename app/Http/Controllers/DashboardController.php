<?php

namespace App\Http\Controllers;

use App\Enums\ShiftStatus;
use App\Enums\TransactionStatus;
use App\Models\Device;
use App\Models\Outlet;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shift;
use App\Models\Stock;
use App\Models\SyncLog;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $outlet = $user->outlet ?? Outlet::first();

        // -------------------------------------------------------------
        // 1. CASHIER ROLE VIEW
        // -------------------------------------------------------------
        if ($user->hasRole('Cashier') && ! $user->hasAnyRole(['Admin', 'Supervisor'])) {
            $activeShift = Shift::where('cashier_id', $user->id)
                ->where('status', ShiftStatus::Open)
                ->latest('opened_at')
                ->first();

            $todayCompletedQuery = Transaction::where('cashier_id', $user->id)
                ->whereDate('transaction_at', Carbon::today())
                ->where('status', TransactionStatus::Completed);

            $cashierRevenue = (float) (clone $todayCompletedQuery)->sum('total');
            $cashierTxCount = (clone $todayCompletedQuery)->count();

            $cashierCashTotal = (float) Payment::whereHas('transaction', function ($q) use ($user): void {
                $q->where('cashier_id', $user->id)
                    ->whereDate('transaction_at', Carbon::today())
                    ->where('status', TransactionStatus::Completed);
            })->where('payment_method', 'cash')->sum('amount');

            $cashierNonCashTotal = max(0, $cashierRevenue - $cashierCashTotal);

            $recentTransactions = Transaction::where('cashier_id', $user->id)
                ->with(['payments', 'customer'])
                ->latest('transaction_at')
                ->take(6)
                ->get();

            return view('dashboard', [
                'roleView' => 'cashier',
                'user' => $user,
                'outlet' => $outlet,
                'activeShift' => $activeShift,
                'cashierRevenue' => $cashierRevenue,
                'cashierTxCount' => $cashierTxCount,
                'cashierCashTotal' => $cashierCashTotal,
                'cashierNonCashTotal' => $cashierNonCashTotal,
                'recentTransactions' => $recentTransactions,
            ]);
        }

        // -------------------------------------------------------------
        // 2. SUPERVISOR ROLE VIEW
        // -------------------------------------------------------------
        if ($user->hasRole('Supervisor') && ! $user->hasRole('Admin')) {
            $outletId = $outlet?->id;

            $todayCompletedQuery = Transaction::when($outletId, fn ($q) => $q->where('outlet_id', $outletId))
                ->whereDate('transaction_at', Carbon::today())
                ->where('status', TransactionStatus::Completed);

            $todayRevenue = (float) (clone $todayCompletedQuery)->sum('total');
            $todayTxCount = (clone $todayCompletedQuery)->count();
            $avgBasket = $todayTxCount > 0 ? $todayRevenue / $todayTxCount : 0;

            $todayVoidCount = Transaction::when($outletId, fn ($q) => $q->where('outlet_id', $outletId))
                ->whereDate('transaction_at', Carbon::today())
                ->where('status', TransactionStatus::Void)
                ->count();

            $activeShifts = Shift::when($outletId, fn ($q) => $q->where('outlet_id', $outletId))
                ->where('status', ShiftStatus::Open)
                ->with(['cashier'])
                ->get();

            $lowStockProducts = Stock::when($outletId, fn ($q) => $q->where('outlet_id', $outletId))
                ->where('quantity', '<=', 10)
                ->with(['product.category'])
                ->orderBy('quantity', 'asc')
                ->take(8)
                ->get();

            $recentTransactions = Transaction::when($outletId, fn ($q) => $q->where('outlet_id', $outletId))
                ->with(['cashier', 'payments'])
                ->latest('transaction_at')
                ->take(6)
                ->get();

            return view('dashboard', [
                'roleView' => 'supervisor',
                'user' => $user,
                'outlet' => $outlet,
                'todayRevenue' => $todayRevenue,
                'todayTxCount' => $todayTxCount,
                'avgBasket' => $avgBasket,
                'todayVoidCount' => $todayVoidCount,
                'activeShifts' => $activeShifts,
                'lowStockProducts' => $lowStockProducts,
                'recentTransactions' => $recentTransactions,
            ]);
        }

        // -------------------------------------------------------------
        // 3. ADMINISTRATOR ROLE VIEW
        // -------------------------------------------------------------
        $todayRevenue = (float) Transaction::where('status', TransactionStatus::Completed)
            ->whereDate('transaction_at', Carbon::today())
            ->sum('total');

        $monthRevenue = (float) Transaction::where('status', TransactionStatus::Completed)
            ->whereBetween('transaction_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum('total');

        $monthTxCount = Transaction::where('status', TransactionStatus::Completed)
            ->whereBetween('transaction_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->count();

        $totalInventoryValue = (float) (Stock::join('products', 'stocks.product_id', '=', 'products.id')
            ->selectRaw('SUM(stocks.quantity * products.purchase_price) as total_val')
            ->value('total_val') ?? 0);

        $totalProducts = Product::where('is_active', true)->count();
        $totalOutlets = Outlet::where('is_active', true)->count();
        $totalDevices = Device::count();
        $totalUsers = User::where('is_active', true)->count();

        $activeShifts = Shift::where('status', ShiftStatus::Open)->with(['cashier', 'outlet'])->get();

        $failedSyncCount = SyncLog::where('status', 'FAILED')->count();

        $lowStockProducts = Stock::where('quantity', '<=', 10)
            ->with(['product.category', 'outlet'])
            ->orderBy('quantity', 'asc')
            ->take(6)
            ->get();

        $recentTransactions = Transaction::with(['cashier', 'outlet', 'payments'])
            ->latest('transaction_at')
            ->take(6)
            ->get();

        return view('dashboard', [
            'roleView' => 'admin',
            'user' => $user,
            'outlet' => $outlet,
            'todayRevenue' => $todayRevenue,
            'monthRevenue' => $monthRevenue,
            'monthTxCount' => $monthTxCount,
            'totalInventoryValue' => $totalInventoryValue,
            'totalProducts' => $totalProducts,
            'totalOutlets' => $totalOutlets,
            'totalDevices' => $totalDevices,
            'totalUsers' => $totalUsers,
            'activeShifts' => $activeShifts,
            'failedSyncCount' => $failedSyncCount,
            'lowStockProducts' => $lowStockProducts,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
