<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable([
    'key',
    'value',
    'type',
    'group',
])]
class Setting extends Model
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('global_settings');
        });

        static::deleted(function () {
            Cache::forget('global_settings');
        });
    }
}
