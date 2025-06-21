<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'notification_time',
        'preferred_categories',
        'device_token',
        'notifications_enabled'
    ];

    protected $casts = [
        'notification_time' => 'string',
        'preferred_categories' => 'array',
        'notifications_enabled' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
