<?php

namespace App\Models;

    use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SiteInfo extends Model
{
    use HasUuids;

    protected $fillable = [
        'site_name',
        'logo_url',
        'favicon_url',
        'prefix',
        'meta_description',
    ];

    protected $casts = [
        'prefix' => 'array',
    ];

    protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                if (empty($model->id)) {
                    $model->id = Str::uuid();
                }
            });
        }

}
