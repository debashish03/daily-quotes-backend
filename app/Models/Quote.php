<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'author',
        'category_id',
        'scheduled_date',
        'is_published',
        'view_count',
        'share_count'
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'is_published' => 'boolean'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(QuoteCategory::class, 'category_id');
    }

    public function shares(): HasMany
    {
        return $this->hasMany(QuoteShare::class);
    }
}
