<?php

namespace App\Models;

use App\Traits\HasCustomUuid;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['uuid', 'name', 'phone', 'email', 'address'])]
class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasCustomUuid, HasFactory, SoftDeletes;

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
