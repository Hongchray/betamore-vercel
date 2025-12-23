<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
class ItemImage extends Model
{
     use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'item_id',
        'image',
        'is_main'
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }

            if ($model->is_main) {
                self::where('item_id', $model->item_id)->update(['is_main' => false]);
            } else {
                $hasMain = self::where('item_id', $model->item_id)->where('is_main', true)->exists();
                if (!$hasMain) {
                    $model->is_main = true;
                }
            }
        });

        static::updating(function ($model) {
            if ($model->is_main) {
                self::where('item_id', $model->item_id)
                    ->where('id', '!=', $model->id)
                    ->update(['is_main' => false]);
            } else {
                $otherMainExists = self::where('item_id', $model->item_id)
                    ->where('id', '!=', $model->id)
                    ->where('is_main', true)
                    ->exists();

                if (!$otherMainExists) {
                    $model->is_main = true; // force at least one main
                }
            }
        });

        static::deleting(function ($model) {
            if ($model->is_main) {
                $otherImage = self::where('item_id', $model->item_id)->where('id', '!=', $model->id)->first();
                if ($otherImage) {
                    $otherImage->is_main = true;
                    $otherImage->save();
                }
            }
        });
    }
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
