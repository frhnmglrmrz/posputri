<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\StockMovementType;
use App\Enums\TransactionStatus;
use App\Models\Outlet;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionService
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    /**
     * Create or sync a transaction idempotently.
     *
     * @param  array<string, mixed>  $data
     */
    public function syncTransaction(array $data): Transaction
    {
        $uuid = $data['uuid'] ?? (string) Str::uuid();

        // 1. Idempotency check: if transaction already exists, return it immediately without duplicate insertion
        $existing = Transaction::with(['items', 'payments'])->where('uuid', $uuid)->first();
        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($data, $uuid): Transaction {
            $outletId = $data['outlet_id'];
            $outlet = Outlet::findOrFail($outletId);

            $transactionNumber = $data['transaction_number'] ?? ('POS-'.date('YmdHis').'-'.strtoupper(Str::random(4)));

            $transaction = Transaction::create([
                'uuid' => $uuid,
                'transaction_number' => $transactionNumber,
                'outlet_id' => $outletId,
                'device_id' => $data['device_id'] ?? null,
                'device_uuid' => $data['device_uuid'] ?? null,
                'cashier_id' => $data['cashier_id'],
                'shift_id' => $data['shift_id'] ?? null,
                'customer_id' => $data['customer_id'] ?? null,
                'subtotal' => $data['subtotal'] ?? 0,
                'discount' => $data['discount'] ?? 0,
                'tax' => $data['tax'] ?? 0,
                'total' => $data['total'] ?? 0,
                'paid_amount' => $data['paid_amount'] ?? $data['total'] ?? 0,
                'change_amount' => $data['change_amount'] ?? 0,
                'status' => TransactionStatus::Completed,
                'transaction_at' => isset($data['transaction_at']) ? Carbon::parse($data['transaction_at']) : now(),
                'synced_at' => now(),
                'notes' => $data['notes'] ?? null,
            ]);

            // Save Items & Record Stock Deductions
            $items = $data['items'] ?? [];
            foreach ($items as $itemData) {
                $productId = $itemData['product_id'];
                $product = Product::find($productId);

                $itemUuid = $itemData['uuid'] ?? (string) Str::uuid();
                $qty = (int) ($itemData['quantity'] ?? 1);
                $price = (float) ($itemData['price'] ?? ($product ? $product->selling_price : 0));
                $discount = (float) ($itemData['discount'] ?? 0);
                $tax = (float) ($itemData['tax'] ?? 0);
                $subtotal = (float) ($itemData['subtotal'] ?? (($price * $qty) - $discount + $tax));

                TransactionItem::create([
                    'uuid' => $itemUuid,
                    'transaction_id' => $transaction->id,
                    'product_id' => $productId,
                    'sku' => $itemData['sku'] ?? ($product?->sku ?? 'UNKNOWN'),
                    'product_name' => $itemData['product_name'] ?? ($product?->name ?? 'Produk'),
                    'price' => $price,
                    'quantity' => $qty,
                    'discount' => $discount,
                    'tax' => $tax,
                    'subtotal' => $subtotal,
                    'notes' => $itemData['notes'] ?? null,
                ]);

                if ($product) {
                    $this->inventoryService->recordSale($outlet, $product, $qty, $uuid);
                }
            }

            // Save Payments
            $payments = $data['payments'] ?? [];
            if (empty($payments)) {
                $payments = [[
                    'payment_method' => $data['payment_method'] ?? 'cash',
                    'amount' => $transaction->paid_amount,
                    'reference' => $data['payment_reference'] ?? null,
                    'status' => 'paid',
                ]];
            }

            foreach ($payments as $payData) {
                $methodCode = $payData['payment_method'] ?? 'cash';
                $methodModel = PaymentMethod::where('code', $methodCode)->first();

                $transaction->payments()->create([
                    'uuid' => $payData['uuid'] ?? (string) Str::uuid(),
                    'payment_method_id' => $methodModel?->id,
                    'payment_method' => $methodCode,
                    'amount' => $payData['amount'] ?? $transaction->total,
                    'reference' => $payData['reference'] ?? null,
                    'status' => PaymentStatus::Paid,
                ]);
            }

            return $transaction->load(['items', 'payments']);
        });
    }

    /**
     * Void a completed transaction.
     */
    public function voidTransaction(Transaction $transaction, ?string $reason = null): bool
    {
        if ($transaction->status === TransactionStatus::Void) {
            return false;
        }

        return DB::transaction(function () use ($transaction, $reason): bool {
            $transaction->status = TransactionStatus::Void;
            $transaction->notes = trim(($transaction->notes ?? '').' [DIBATALKAN: '.($reason ?? 'Tanpa alasan').']');
            $transaction->save();

            // Return stock
            $outlet = $transaction->outlet;
            foreach ($transaction->items as $item) {
                $product = $item->product;
                if ($product && $outlet) {
                    $this->inventoryService->adjustStock(
                        outlet: $outlet,
                        product: $product,
                        quantityChange: $item->quantity,
                        type: StockMovementType::ReturnOrder,
                        referenceType: 'VOID_TRANSACTION',
                        referenceUuid: $transaction->uuid,
                        notes: "Pembatalan transaksi #{$transaction->transaction_number}"
                    );
                }
            }

            return true;
        });
    }
}
