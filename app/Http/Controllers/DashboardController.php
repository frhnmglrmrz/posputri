<?php

namespace App\Http\Controllers;

use App\Enums\ShiftStatus;
use App\Enums\TransactionStatus;
use App\Models\Category;
use App\Models\Device;
use App\Models\Outlet;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shift;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\SyncLog;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
        // 3. INVENTORY / WAREHOUSE ROLE VIEW
        // -------------------------------------------------------------
        if ($user->hasRole('Inventory') && ! $user->hasAnyRole(['Admin', 'Supervisor'])) {
            $outletId = $outlet?->id;

            $totalProducts = Product::where('is_active', true)->count();
            $totalCategories = Category::count();
            $totalStock = (int) Stock::when($outletId, fn ($q) => $q->where('outlet_id', $outletId))->sum('quantity');
            $lowStockCount = Stock::when($outletId, fn ($q) => $q->where('outlet_id', $outletId))
                ->where('quantity', '>', 0)
                ->where('quantity', '<=', 10)
                ->count();
            $outOfStockCount = Stock::when($outletId, fn ($q) => $q->where('outlet_id', $outletId))
                ->where('quantity', '<=', 0)
                ->count();

            $lowStockProducts = Stock::when($outletId, fn ($q) => $q->where('outlet_id', $outletId))
                ->where('quantity', '<=', 10)
                ->with(['product.category'])
                ->orderBy('quantity', 'asc')
                ->take(8)
                ->get();

            $recentMovements = StockMovement::when($outletId, fn ($q) => $q->where('outlet_id', $outletId))
                ->with(['product'])
                ->latest()
                ->take(6)
                ->get();

            $recentProducts = Product::with(['category', 'stocks' => fn ($q) => $outletId ? $q->where('outlet_id', $outletId) : $q])
                ->latest()
                ->take(6)
                ->get();

            return view('dashboard', [
                'roleView' => 'inventory',
                'user' => $user,
                'outlet' => $outlet,
                'totalProducts' => $totalProducts,
                'totalCategories' => $totalCategories,
                'totalStock' => $totalStock,
                'lowStockCount' => $lowStockCount,
                'outOfStockCount' => $outOfStockCount,
                'lowStockProducts' => $lowStockProducts,
                'recentMovements' => $recentMovements,
                'recentProducts' => $recentProducts,
            ]);
        }

        // -------------------------------------------------------------
        // 4. OWNER ROLE VIEW (Read-only Business Analytics & Charts)
        // -------------------------------------------------------------
        if ($user->hasRole('Owner') && ! $user->hasRole('Admin')) {
            $analytics = $this->getAnalyticsData($request);

            return view('dashboard', [
                'roleView' => 'owner',
                'user' => $user,
                'outlet' => $outlet,
                ...$analytics,
            ]);
        }

        // -------------------------------------------------------------
        // 5. ADMINISTRATOR ROLE VIEW
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

        $analytics = $this->getAnalyticsData($request);

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
            ...$analytics,
        ]);
    }

    /**
     * Compute actual analytics data, trends, and charts for executive/owner dashboard.
     *
     * @return array<string, mixed>
     */
    private function getAnalyticsData(Request $request): array
    {
        $period = $request->input('period', '7_days');
        $now = Carbon::now();

        switch ($period) {
            case 'today':
                $start = Carbon::today()->startOfDay();
                $end = Carbon::today()->endOfDay();
                $prevStart = Carbon::yesterday()->startOfDay();
                $prevEnd = Carbon::yesterday()->endOfDay();
                $periodLabel = 'Hari Ini ('.$now->format('d M Y').')';
                break;
            case '30_days':
                $start = Carbon::now()->subDays(29)->startOfDay();
                $end = Carbon::now()->endOfDay();
                $prevStart = Carbon::now()->subDays(59)->startOfDay();
                $prevEnd = Carbon::now()->subDays(30)->endOfDay();
                $periodLabel = '30 Hari Terakhir';
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth()->startOfDay();
                $end = Carbon::now()->endOfMonth()->endOfDay();
                $prevStart = Carbon::now()->subMonth()->startOfMonth()->startOfDay();
                $prevEnd = Carbon::now()->subMonth()->endOfMonth()->endOfDay();
                $periodLabel = 'Bulan Ini ('.$now->format('F Y').')';
                break;
            case 'this_year':
                $start = Carbon::now()->startOfYear()->startOfDay();
                $end = Carbon::now()->endOfYear()->endOfDay();
                $prevStart = Carbon::now()->subYear()->startOfYear()->startOfDay();
                $prevEnd = Carbon::now()->subYear()->endOfYear()->endOfDay();
                $periodLabel = 'Tahun Ini ('.$now->format('Y').')';
                break;
            case 'custom':
                $start = Carbon::parse($request->input('start_date', Carbon::today()->subDays(6)->toDateString()))->startOfDay();
                $end = Carbon::parse($request->input('end_date', Carbon::today()->toDateString()))->endOfDay();
                $diffDays = max(1, $start->diffInDays($end));
                $prevEnd = (clone $start)->subSecond();
                $prevStart = (clone $prevEnd)->subDays($diffDays)->startOfDay();
                $periodLabel = $start->format('d/m/Y').' - '.$end->format('d/m/Y');
                break;
            case '7_days':
            default:
                $period = '7_days';
                $start = Carbon::now()->subDays(6)->startOfDay();
                $end = Carbon::now()->endOfDay();
                $prevStart = Carbon::now()->subDays(13)->startOfDay();
                $prevEnd = Carbon::now()->subDays(7)->endOfDay();
                $periodLabel = '7 Hari Terakhir';
                break;
        }

        $baseQuery = Transaction::where('status', TransactionStatus::Completed)
            ->whereBetween('transaction_at', [$start, $end]);

        $periodRevenue = (float) (clone $baseQuery)->sum('total');
        $periodTxCount = (clone $baseQuery)->count();
        $periodAvgBasket = $periodTxCount > 0 ? (float) ($periodRevenue / $periodTxCount) : 0.0;

        $prevRevenue = (float) Transaction::where('status', TransactionStatus::Completed)
            ->whereBetween('transaction_at', [$prevStart, $prevEnd])
            ->sum('total');

        $prevTxCount = Transaction::where('status', TransactionStatus::Completed)
            ->whereBetween('transaction_at', [$prevStart, $prevEnd])
            ->count();

        $revenueGrowth = $prevRevenue > 0
            ? round((($periodRevenue - $prevRevenue) / $prevRevenue) * 100, 1)
            : ($periodRevenue > 0 ? 100.0 : 0.0);

        $txGrowth = $prevTxCount > 0
            ? round((($periodTxCount - $prevTxCount) / $prevTxCount) * 100, 1)
            : ($periodTxCount > 0 ? 100.0 : 0.0);

        // Total products sold
        $itemsSold = (int) TransactionItem::whereHas('transaction', function ($q) use ($start, $end): void {
            $q->where('status', TransactionStatus::Completed)
                ->whereBetween('transaction_at', [$start, $end]);
        })->sum('quantity');

        // Gross Margin Estimation
        $estimatedCost = (float) (TransactionItem::whereHas('transaction', function ($q) use ($start, $end): void {
            $q->where('status', TransactionStatus::Completed)
                ->whereBetween('transaction_at', [$start, $end]);
        })->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->selectRaw('SUM(transaction_items.quantity * products.purchase_price) as cost')
            ->value('cost') ?? 0);

        $estimatedProfit = max(0, $periodRevenue - $estimatedCost);
        $profitMarginPercent = $periodRevenue > 0 ? round(($estimatedProfit / $periodRevenue) * 100, 1) : 0.0;

        // Daily / Interval Trend Points
        $trendPoints = [];
        $maxRevenueInPoint = 0;
        $maxTxInPoint = 0;

        if ($period === 'this_year') {
            for ($m = 1; $m <= 12; $m++) {
                $mStart = Carbon::createFromDate($start->year, $m, 1)->startOfDay();
                $mEnd = (clone $mStart)->endOfMonth()->endOfDay();
                $rev = (float) Transaction::where('status', TransactionStatus::Completed)
                    ->whereBetween('transaction_at', [$mStart, $mEnd])
                    ->sum('total');
                $cnt = Transaction::where('status', TransactionStatus::Completed)
                    ->whereBetween('transaction_at', [$mStart, $mEnd])
                    ->count();

                $maxRevenueInPoint = max($maxRevenueInPoint, $rev);
                $maxTxInPoint = max($maxTxInPoint, $cnt);

                $trendPoints[] = [
                    'label' => $mStart->format('M'),
                    'full_label' => $mStart->format('F Y'),
                    'revenue' => $rev,
                    'tx_count' => $cnt,
                ];
            }
        } else {
            $currentCursor = (clone $start)->startOfDay();
            $endCursor = (clone $end)->endOfDay();

            while ($currentCursor <= $endCursor) {
                $dayStart = (clone $currentCursor)->startOfDay();
                $dayEnd = (clone $currentCursor)->endOfDay();

                $rev = (float) Transaction::where('status', TransactionStatus::Completed)
                    ->whereBetween('transaction_at', [$dayStart, $dayEnd])
                    ->sum('total');
                $cnt = Transaction::where('status', TransactionStatus::Completed)
                    ->whereBetween('transaction_at', [$dayStart, $dayEnd])
                    ->count();

                $maxRevenueInPoint = max($maxRevenueInPoint, $rev);
                $maxTxInPoint = max($maxTxInPoint, $cnt);

                $trendPoints[] = [
                    'label' => $currentCursor->format('d/m'),
                    'full_label' => $currentCursor->format('d M Y'),
                    'revenue' => $rev,
                    'tx_count' => $cnt,
                ];
                $currentCursor->addDay();
            }
        }

        // Top 6 Products in Period
        $topProducts = TransactionItem::select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_subtotal'))
            ->whereHas('transaction', function ($q) use ($start, $end): void {
                $q->where('status', TransactionStatus::Completed)
                    ->whereBetween('transaction_at', [$start, $end]);
            })
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->take(6)
            ->get();

        $maxProductQty = $topProducts->first()?->total_qty ?? 1;

        // Payment Methods Breakdown
        $paymentMethods = Payment::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->whereHas('transaction', function ($q) use ($start, $end): void {
                $q->where('status', TransactionStatus::Completed)
                    ->whereBetween('transaction_at', [$start, $end]);
            })
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get()
            ->map(function ($pm) use ($periodRevenue) {
                $pct = $periodRevenue > 0 ? round(($pm->total / $periodRevenue) * 100, 1) : 0;

                return [
                    'method' => strtoupper($pm->payment_method),
                    'count' => (int) $pm->count,
                    'total' => (float) $pm->total,
                    'percentage' => $pct,
                ];
            });

        // Recent Completed Transactions
        $recentTransactions = Transaction::where('status', TransactionStatus::Completed)
            ->with(['cashier', 'payments', 'customer'])
            ->latest('transaction_at')
            ->take(6)
            ->get();

        return [
            'period' => $period,
            'periodLabel' => $periodLabel,
            'startDate' => $start->toDateString(),
            'endDate' => $end->toDateString(),
            'periodRevenue' => $periodRevenue,
            'periodTxCount' => $periodTxCount,
            'periodAvgBasket' => $periodAvgBasket,
            'revenueGrowth' => $revenueGrowth,
            'txGrowth' => $txGrowth,
            'itemsSold' => $itemsSold,
            'estimatedProfit' => $estimatedProfit,
            'profitMarginPercent' => $profitMarginPercent,
            'trendPoints' => $trendPoints,
            'maxRevenueInPoint' => $maxRevenueInPoint,
            'maxTxInPoint' => $maxTxInPoint,
            'topProducts' => $topProducts,
            'maxProductQty' => $maxProductQty,
            'paymentMethods' => $paymentMethods,
            'recentTransactions' => $recentTransactions,
        ];
    }
}
