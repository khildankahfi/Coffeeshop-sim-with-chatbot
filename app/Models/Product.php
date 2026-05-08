<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['category_id', 'name', 'description', 'price', 'image_url', 'is_available'];

    protected $casts = ['price' => 'float', 'is_available' => 'boolean'];

    // Scope: hanya produk yang tersedia
    public function scopeAvailable($query) {
        return $query->where('is_available', true);
    }

    public function category(): BelongsTo {
        return $this->belongsTo(Category::class);
    }
}
