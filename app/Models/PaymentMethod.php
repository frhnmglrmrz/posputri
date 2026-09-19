<?php

namespace App\Models;

use App\Traits\HasCustomUuid;
use Database\Factories\PaymentMethodFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['uuid', 'code', 'name', 'offline_available', 'is_active'])]
class PaymentMethod extends Model
{
    /** @use HasFactory<PaymentMethodFactory> */
    use HasCustomUuid, HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'offline_available' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
