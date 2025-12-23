<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    // Use UUID instead of auto-increment ID
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'company_id',
        'name_en',
        'name_km',
        'description_en',
        'description_km',
        'logo',
    ];

    // Set UUID automatically when creating a new model
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // Relationship: Company has many Items
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    protected $dates = ['deleted_at'];
}
