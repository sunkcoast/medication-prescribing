<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    /**
     * Boot trait ini secara otomatis untuk model yang menggunakannya.
     * Laravel mengenali method dengan prefix 'boot' + nama Trait.
     */
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            ActivityLog::create([
                'user_id'    => auth()->id() ?? null,
                'action'     => 'CREATED',
                'model_type' => get_class($model),
                'model_id'   => $model->id,
                'after'      => $model->toArray(),
                'ip_address' => request()->ip(),
            ]);
        });

        static::updated(function ($model) {
            if ($model->getDirty()) {
                ActivityLog::create([
                    'user_id'    => auth()->id() ?? null,
                    'action'     => 'UPDATED',
                    'model_type' => get_class($model),
                    'model_id'   => $model->id,
                    'before'     => array_intersect_key($model->getOriginal(), $model->getDirty()),
                    'after'      => $model->getDirty(),
                    'ip_address' => request()->ip(),
                ]);
            }
        });

        static::deleted(function ($model) {
            ActivityLog::create([
                'user_id'    => auth()->id() ?? null,
                'action'     => 'DELETED',
                'model_type' => get_class($model),
                'model_id'   => $model->id,
                'before'     => $model->toArray(),
                'ip_address' => request()->ip(),
            ]);
        });
    }
}