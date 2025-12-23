<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ItemImage;
use App\Models\ItemModification;
use App\Models\Company;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use SoftDeletes, HasUuids;

    protected $table = 'items';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'item_id',
        'name_en',
        'name_km',
        'description_en',
        'description_km',
        'company_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
    public function firstImage()
    {
        return $this->hasOne(ItemImage::class)->orderBy('created_at');
    }

    /**
     * Relationships
     */
    public function modifications()
    {
        return $this->hasMany(ItemModification::class);
    }
    
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function images()
    {
        return $this->hasMany(ItemImage::class);
    }

    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class, 'item_promotion', 'item_id', 'promotion_id');
    }

    public function itemPromotions(): HasMany
    {
        return $this->hasMany(ItemPromotion::class, 'item_id');
    }

    public function orderItems()
    {
        return $this->hasManyThrough(OrderItem::class, ItemModification::class);
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, ItemModification::class, 'item_id', 'id', 'id', 'order_id')
            ->through(OrderItem::class);
    }
}
