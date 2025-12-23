<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Item;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;


class Promotion extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'promotion_id',
        'name_en',
        'name_km',
        'banner',
        'description_en',
        'description_km',
        'type',
        'discount_value',
        'start_date',
        'end_date',
        'status'
    ];

    protected $casts = [

        'start_date' => 'date',
        'end_date' => 'date',
    ];


    protected static function booted(): void
    {
        static::saving(function ($promotion) {
            $now = now();

            if ($promotion->start_date && $now->lt($promotion->start_date)) {
                $promotion->status = 'upcoming';
            } elseif ($promotion->end_date && $now->gt($promotion->end_date)) {
                $promotion->status = 'expired';
            } else {
                $promotion->status = 'active';
            }
        });
    }



    /**
     * Get the items associated with this promotion
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_promotion', 'promotion_id', 'item_id');
    }

    public function itemPromotions(): HasMany
    {
        return $this->hasMany(ItemPromotion::class, 'promotion_id');
    }

}
