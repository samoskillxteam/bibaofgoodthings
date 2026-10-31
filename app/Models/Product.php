<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'product_type',
        'short_description',
        'full_description',
        'base_price',
        'sale_price',
        'cost_price',
        'stock_quantity',
        'track_stock',
        'is_available',
        'is_featured',
        'display_order',
        'meta_title',
        'meta_description',
    ];

    protected $hidden = [
        'cost_price',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'track_stock' => 'boolean',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'display_order' => 'integer',
    ];

    protected $appends = [
        'effective_price',
        'primary_image_url',
        'primary_video_url',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class)->orderBy('display_order');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductMedia::class)
            ->where('media_type', 'image')
            ->orderBy('display_order');
    }

    public function videos(): HasMany
    {
        return $this->hasMany(ProductMedia::class)
            ->where('media_type', 'video')
            ->orderBy('display_order');
    }

    public function primaryMedia(): HasOne
    {
        return $this->hasOne(ProductMedia::class)
            ->where('is_primary', true)
            ->oldestOfMany();
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class)->orderBy('display_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(ProductAddon::class)->orderBy('display_order');
    }

    public function getEffectivePriceAttribute(): float
    {
        if ($this->sale_price !== null && $this->sale_price > 0 && $this->sale_price < $this->base_price) {
            return (float) $this->sale_price;
        }

        return (float) $this->base_price;
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        $primary = $this->media->firstWhere('is_primary', true);
        if ($primary && $primary->media_type === 'image') {
            return $primary->url;
        }

        $firstImage = $this->media->firstWhere('media_type', 'image');
        if ($firstImage) {
            return $firstImage->url;
        }

        return null;
    }

    public function getPrimaryVideoUrlAttribute(): ?string
    {
        $video = $this->media->firstWhere('media_type', 'video');
        return $video ? $video->url : null;
    }
}
