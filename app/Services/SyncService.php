<?php

namespace App\Services;

use App\Enums\ShiftStatus;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Outlet;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\SyncLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Throwable;

class SyncService
{
    public function __construct(
        protected TransactionService $transactionService
    ) {}

    /**
     * Get complete bootstrap master data for initializing client IndexedDB.
     *
     * @return array<string, mixed>
     */
    public function getBootstrapData(Outlet $outlet): array
    {
        return [
            'timestamp' => now()->toIso8601String(),
            'server_timestamp' => now()->toIso8601String(),
            'outlet' => [
                'id' => $outlet->id,
                'uuid' => $outlet->uuid,
                'name' => $outlet->name,
                'code' => $outlet->code,
            ],
            'categories' => Category::where('is_active', true)->get([
                'id', 'uuid', 'name', 'slug', 'is_active', 'updated_at',
            ]),
            'products' => Product::with(['stocks' => function ($q) use ($outlet): void {
                $q->where('outlet_id', $outlet->id);
            }])
                ->where('is_active', true)
                ->get()
                ->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'uuid' => $p->uuid,
                        'category_id' => $p->category_id,
                        'sku' => $p->sku,
                        'barcode' => $p->barcode,
                        'name' => $p->name,
                        'purchase_price' => (float) $p->purchase_price,
                        'selling_price' => (float) $p->selling_price,
                        'tax_rate' => (float) $p->tax_rate,
                        'is_active' => (bool) $p->is_active,
                        'stock' => (int) ($p->stocks->first()?->quantity ?? 0),
                        'updated_at' => $p->updated_at?->toIso8601String(),
                    ];
                }),
            'payment_methods' => PaymentMethod::where('is_active', true)->get([
                'id', 'uuid', 'code', 'name', 'offline_available', 'is_active',
            ]),
            'customers' => Customer::all([
                'id', 'uuid', 'name', 'phone', 'email', 'address',
            ]),
            'settings' => Setting::all(['key', 'value', 'group'])->pluck('value', 'key'),
        ];
    }

    /**
     * Get incremental changes since a specific timestamp.
     *
     * @return array<string, mixed>
     */
    public function getChanges(Outlet $outlet, ?string $since = null): array
    {
        $sinceDate = $since ? Carbon::parse($since) : Carbon::createFromTimestamp(0);

        $categories = Category::withTrashed()
            ->where('updated_at', '>=', $sinceDate)
            ->get();

        $products = Product::withTrashed()
            ->with(['stocks' => function ($q) use ($outlet): void {
                $q->where('outlet_id', $outlet->id);
            }])
            ->where('updated_at', '>=', $sinceDate)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'uuid' => $p->uuid,
                'category_id' => $p->category_id,
                'sku' => $p->sku,
                'barcode' => $p->barcode,
                'name' => $p->name,
                'selling_price' => (float) $p->selling_price,
                'is_active' => (bool) $p->is_active,
                'stock' => (int) ($p->stocks->first()?->quantity ?? 0),
                'deleted_at' => $p->deleted_at?->toIso8601String(),
                'updated_at' => $p->updated_at?->toIso8601String(),
            ]);

        $paymentMethods = PaymentMethod::where('updated_at', '>=', $sinceDate)->get();

        return [
            'timestamp' => now()->toIso8601String(),
            'server_timestamp' => now()->toIso8601String(),
            'categories' => $categories,
            'products' => $products,
            'payment_methods' => $paymentMethods,
        ];
    }

    /**
     * Process incoming batch of offline transactions.
     *
     * @param  array<int, array<string, mixed>>  $transactions
     * @return array<string, mixed>
     */
    public function syncTransactions(array $transactions, ?int $outletId = null, ?string $deviceUuid = null): array
    {
        $synced = [];
        $failed = [];

        foreach ($transactions as $txData) {
            $uuid = $txData['uuid'] ?? null;
            if (! $uuid) {
                continue;
            }

            if ($outletId && empty($txData['outlet_id'])) {
                $txData['outlet_id'] = $outletId;
            }

            if ($deviceUuid && empty($txData['device_uuid'])) {
                $txData['device_uuid'] = $deviceUuid;
            }

            try {
                $transaction = $this->transactionService->syncTransaction($txData);

                SyncLog::create([
                    'uuid' => (string) Str::uuid(),
                    'outlet_id' => $transaction->outlet_id,
                    'device_id' => $transaction->device_id,
                    'device_uuid' => $transaction->device_uuid,
                    'entity_type' => 'TRANSACTION',
                    'entity_uuid' => $uuid,
                    'operation' => 'CREATE',
                    'payload' => $txData,
                    'status' => 'SYNCED',
                    'error_message' => null,
                ]);

                $synced[] = [
                    'uuid' => $uuid,
                    'server_id' => $transaction->id,
                    'transaction_number' => $transaction->transaction_number,
                    'status' => 'SYNCED',
                ];
            } catch (Throwable $e) {
                SyncLog::create([
                    'uuid' => (string) Str::uuid(),
                    'outlet_id' => $outletId,
                    'device_uuid' => $deviceUuid,
                    'entity_type' => 'TRANSACTION',
                    'entity_uuid' => $uuid,
                    'operation' => 'CREATE',
                    'payload' => $txData,
                    'status' => 'FAILED',
                    'error_message' => $e->getMessage(),
                ]);

                $failed[] = [
                    'uuid' => $uuid,
                    'error' => $e->getMessage(),
                    'status' => 'FAILED',
                ];
            }
        }

        return [
            'synced' => $synced,
            'failed' => $failed,
            'total_processed' => count($transactions),
            'total_synced' => count($synced),
            'total_failed' => count($failed),
        ];
    }

    /**
     * Process incoming shift record from client.
     *
     * @param  array<string, mixed>  $data
     */
    public function syncShift(array $data): Shift
    {
        $uuid = $data['uuid'] ?? (string) Str::uuid();

        return Shift::updateOrCreate(
            ['uuid' => $uuid],
            [
                'outlet_id' => $data['outlet_id'],
                'device_id' => $data['device_id'] ?? null,
                'cashier_id' => $data['cashier_id'],
                'opening_cash' => $data['opening_cash'] ?? 0,
                'expected_cash' => $data['expected_cash'] ?? 0,
                'closing_cash' => $data['closing_cash'] ?? null,
                'difference' => $data['difference'] ?? 0,
                'opened_at' => isset($data['opened_at']) ? Carbon::parse($data['opened_at']) : now(),
                'closed_at' => isset($data['closed_at']) ? Carbon::parse($data['closed_at']) : null,
                'status' => ($data['status'] ?? 'open') === 'closed' ? ShiftStatus::Closed : ShiftStatus::Open,
                'notes' => $data['notes'] ?? null,
            ]
        );
    }
}
