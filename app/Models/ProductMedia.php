<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductMedia extends Model
{
    use HasFactory;

    protected $table = 'product_media';

    protected $fillable = [
        'product_id',
        'file_path',
        'media_type',
        'alt_text',
        'display_order',
        'is_primary',
        'thumbnail_path',
        'file_size',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'display_order' => 'integer',
        'file_size' => 'integer',
    ];

    protected $appends = ['url'];

    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://')) {
            return $this->file_path;
        }

        return asset('storage/' . ltrim($this->file_path, '/'));
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
