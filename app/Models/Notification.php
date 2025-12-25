<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Notification extends Model
{
    public $incrementing = false;
    protected $keyType = 'string'; 

    protected $fillable = [
        'id',
        'user_id',
        'type',
        'title',
        'body',
        'data',
        'is_read',
        'is_sent',
        'read_at',
        'sent_at',
    ];

    protected $casts = [
        'data'     => 'array',
        'is_read'  => 'boolean',  // ✅ Use Laravel's built-in boolean cast
        'is_sent'  => 'boolean',  // ✅ Use Laravel's built-in boolean cast
        'read_at'  => 'datetime',
        'sent_at'  => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function markAsSent(): void
    {
        if (!$this->is_sent) {
            $this->update([
                'is_sent' => true,
                'sent_at' => now(),
            ]);
        }
    }

     public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }


    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }
}