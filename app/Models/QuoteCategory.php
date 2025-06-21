<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'image',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'category_id');
    }

    /**
     * Get the full URL for the category image
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        return asset('storage/' . $this->image);
    }

    /**
     * Check if the category has an image
     */
    public function hasImage(): bool
    {
        return !empty($this->image);
    }
}
