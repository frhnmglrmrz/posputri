<?php

namespace App\Models;

use App\Traits\HasCustomUuid;
use Database\Factories\TransactionItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['uuid', 'transaction_id', 'product_id', 'sku', 'product_name', 'price', 'quantity', 'discount', 'tax', 'subtotal', 'notes'])]
class TransactionItem extends Model
{
    /** @use HasFactory<TransactionItemFactory> */
    use HasCustomUuid, HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'quantity' => 'integer',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
