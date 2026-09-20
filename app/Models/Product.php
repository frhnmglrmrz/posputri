<?php

namespace App\Models;

use App\Traits\HasCustomUuid;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['uuid', 'category_id', 'sku', 'barcode', 'name', 'purchase_price', 'selling_price', 'tax_rate', 'is_active'])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasCustomUuid, HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            if (empty($product->sku)) {
                $product->sku = static::generateNextSku();
            }
        });
    }

    public static function generateNextSku(): string
    {
        $maxNum = 0;
        $skus = static::withTrashed()
            ->where('sku', 'like', 'PRD-%')
            ->pluck('sku');

        foreach ($skus as $sku) {
            if (preg_match('/^PRD-(\d+)$/i', $sku, $matches)) {
                $num = (int) $matches[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }

        return 'PRD-'.str_pad((string) ($maxNum + 1), 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }
}
