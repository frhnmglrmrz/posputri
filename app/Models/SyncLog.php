<?php

namespace App\Models;

use App\Traits\HasCustomUuid;
use Database\Factories\SyncLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['uuid', 'outlet_id', 'device_id', 'device_uuid', 'entity_type', 'entity_uuid', 'operation', 'payload', 'status', 'error_message'])]
class SyncLog extends Model
{
    /** @use HasFactory<SyncLogFactory> */
    use HasCustomUuid, HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
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
}
