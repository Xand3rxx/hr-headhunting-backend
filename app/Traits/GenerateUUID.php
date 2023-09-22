<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GenerateUUID
{
    protected static function bootGenerateUuid()
    {
        static::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
        });
    }
}
