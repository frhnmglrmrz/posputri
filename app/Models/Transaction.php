<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Traits\HasCustomUuid;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['uuid', 'transaction_number', 'outlet_id', 'device_id', 'device_uuid', 'cashier_id', 'shift_id', 'customer_id', 'subtotal', 'discount', 'tax', 'total', 'paid_amount', 'change_amount', 'status', 'transaction_at', 'synced_at', 'notes'])]
class Transaction extends Model
{
    /** @use HasFactory<TransactionFactory> */
    use HasCustomUuid, HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Transaction $transaction): void {
            if (empty($transaction->transaction_number)) {
                $transaction->transaction_number = static::generateNextNumber();
            }
        });
    }

    public static function generateNextNumber(): string
    {
        $prefix = 'TRX-'.date('Ymd').'-';
        do {
            $number = $prefix.strtoupper(Str::random(4));
        } while (static::where('transaction_number', $number)->exists());

        return $number;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'transaction_at' => 'datetime',
            'synced_at' => 'datetime',
            'status' => TransactionStatus::class,
        ];
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
