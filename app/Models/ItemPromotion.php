<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemPromotion extends Model
{
    protected $table = 'item_promotion';
    
    // Since this is a pivot table, we don't need UUID primary key
    public $incrementing = false;
    protected $keyType = 'string';
    
    // Define the composite primary key
    protected $primaryKey = ['item_id', 'promotion_id'];
    
    protected $fillable = [
        'item_id',
        'promotion_id',
    ];

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = true;

    /**
     * Get the item that belongs to this promotion relationship
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * Get the promotion that belongs to this item relationship
     */
    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class, 'promotion_id');
    }
}
