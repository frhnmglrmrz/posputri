<?php

namespace App\Models;

use App\Enums\ShiftStatus;
use App\Traits\HasCustomUuid;
use Database\Factories\ShiftFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['uuid', 'outlet_id', 'device_id', 'cashier_id', 'opening_cash', 'expected_cash', 'closing_cash', 'difference', 'opened_at', 'closed_at', 'status', 'notes'])]
class Shift extends Model
{
    /** @use HasFactory<ShiftFactory> */
    use HasCustomUuid, HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'opening_cash' => 'decimal:2',
            'expected_cash' => 'decimal:2',
            'closing_cash' => 'decimal:2',
            'difference' => 'decimal:2',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'status' => ShiftStatus::class,
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

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
