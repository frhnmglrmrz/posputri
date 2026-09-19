<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryService
{
    /**
     * Adjust product stock for an outlet and record a movement.
     */
    public function adjustStock(
        Outlet $outlet,
        Product $product,
        int $quantityChange,
        StockMovementType $type,
        ?string $referenceType = null,
        ?string $referenceUuid = null,
        ?string $notes = null
    ): StockMovement {
        return DB::transaction(function () use ($outlet, $product, $quantityChange, $type, $referenceType, $referenceUuid, $notes): StockMovement {
            $stock = Stock::firstOrCreate(
                [
                    'outlet_id' => $outlet->id,
                    'product_id' => $product->id,
                ],
                [
                    'quantity' => 0,
                ]
            );

            $stock->increment('quantity', $quantityChange);

            return StockMovement::create([
                'uuid' => (string) Str::uuid(),
                'outlet_id' => $outlet->id,
                'product_id' => $product->id,
                'reference_type' => $referenceType,
                'reference_uuid' => $referenceUuid,
                'type' => $type,
                'quantity' => $quantityChange,
                'notes' => $notes,
            ]);
        });
    }

    /**
     * Record inventory decrement for a sale.
     */
    public function recordSale(Outlet $outlet, Product $product, int $quantity, string $transactionUuid): StockMovement
    {
        return $this->adjustStock(
            outlet: $outlet,
            product: $product,
            quantityChange: -$quantity,
            type: StockMovementType::Sale,
            referenceType: 'TRANSACTION',
            referenceUuid: $transactionUuid,
            notes: "Penjualan POS #{$transactionUuid}"
        );
    }

    /**
     * Get current stock quantity for an outlet and product.
     */
    public function getCurrentStock(Outlet $outlet, Product $product): int
    {
        $stock = Stock::where('outlet_id', $outlet->id)
            ->where('product_id', $product->id)
            ->first();

        return $stock ? (int) $stock->quantity : 0;
    }
}
