<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasCustomUuid
{
    /**
     * Initialize the trait.
     */
    public function initializeHasCustomUuid(): void
    {
        if (empty($this->attributes['uuid'] ?? null)) {
            $this->attributes['uuid'] = (string) Str::uuid();
        }
    }

    /**
     * Boot the custom UUID trait for the model.
     */
    protected static function bootHasCustomUuid(): void
    {
        static::creating(function ($model): void {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
