<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class BooleanCast implements CastsAttributes
{
    /**
     * Cast the given value (from database to model)
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): bool
    {
        return (bool) $value;
    }

    /**
     * Prepare the given value for storage (from model to database)
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): bool
    {
        // This is the key - convert to actual boolean for PostgreSQL
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}