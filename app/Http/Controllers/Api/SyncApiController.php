<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Services\SyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SyncApiController extends Controller
{
    public function __construct(
        protected SyncService $syncService
    ) {}

    /**
     * Health check endpoint (PRD Section 31).
     */
    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Bootstrap master data download for IndexedDB initialization (PRD Section 49).
     */
    public function bootstrap(Request $request): JsonResponse
    {
        $outletId = $request->query('outlet_id') ?? $request->user()?->outlet_id;
        $outlet = $outletId ? Outlet::find($outletId) : Outlet::first();

        if (! $outlet) {
            $outlet = Outlet::firstOrCreate(
                ['code' => 'OUT-001'],
                ['name' => 'Outlet Utama', 'is_active' => true]
            );
        }

        $data = $this->syncService->getBootstrapData($outlet);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Incremental sync of master data changes (PRD Section 50).
     */
    public function changes(Request $request): JsonResponse
    {
        $outletId = $request->query('outlet_id') ?? $request->user()?->outlet_id;
        $outlet = $outletId ? Outlet::find($outletId) : Outlet::first();

        if (! $outlet) {
            return response()->json(['error' => 'Outlet not found'], 404);
        }

        $since = $request->query('since');
        $changes = $this->syncService->getChanges($outlet, $since);

        return response()->json([
            'success' => true,
            'data' => $changes,
        ]);
    }

    /**
     * Synchronize batch or single offline transactions (PRD Section 33, 34).
     */
    public function syncTransactions(Request $request): JsonResponse
    {
        $outletId = $request->input('outlet_id') ?? $request->user()?->outlet_id ?? Outlet::value('id');
        $deviceUuid = $request->input('device_uuid') ?? $request->header('X-Device-UUID');

        // Handle both single transaction object or { transactions: [...] } array
        if ($request->has('transactions') && is_array($request->input('transactions'))) {
            $transactions = $request->input('transactions');
        } elseif ($request->has('uuid')) {
            $transactions = [$request->all()];
        } else {
            $transactions = [];
        }

        $result = $this->syncService->syncTransactions($transactions, $outletId, $deviceUuid);

        return response()->json([
            'success' => true,
            'result' => $result,
        ]);
    }

    /**
     * Synchronize cashier shift record (PRD Section 42, 43).
     */
    public function syncShift(Request $request): JsonResponse
    {
        $data = $request->validate([
            'uuid' => ['required', 'string'],
            'outlet_id' => ['required', 'integer'],
            'device_id' => ['nullable', 'integer'],
            'cashier_id' => ['required', 'integer'],
            'opening_cash' => ['nullable', 'numeric'],
            'expected_cash' => ['nullable', 'numeric'],
            'closing_cash' => ['nullable', 'numeric'],
            'difference' => ['nullable', 'numeric'],
            'opened_at' => ['nullable', 'string'],
            'closed_at' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $shift = $this->syncService->syncShift($data);

        return response()->json([
            'success' => true,
            'shift' => $shift,
        ]);
    }
}
