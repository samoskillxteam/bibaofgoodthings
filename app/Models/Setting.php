<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = self::getAllCached();
        return $all[$key] ?? $default;
    }

    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'text', ?string $description = null): self
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'group' => $group,
                'type' => $type,
                'description' => $description,
            ]
        );

        Cache::forget('app_settings');

        return $setting;
    }

    public static function getAllCached(): array
    {
        return Cache::rememberForever('app_settings', function () {
            return self::pluck('value', 'key')->toArray();
        });
    }

    public static function getPublicSettings(): array
    {
        $all = self::getAllCached();

        return [
            'business_name' => $all['business_name'] ?? 'Biba of Good Things',
            'clothing_brand_name' => $all['clothing_brand_name'] ?? 'Biba Clothings',
            'confectionery_brand_name' => $all['confectionery_brand_name'] ?? 'Biba Treats',
            'tagline' => $all['tagline'] ?? 'Exquisite Fashion & Handcrafted Confectioneries',
            'hero_headline' => $all['hero_headline'] ?? 'Where Style Meets Sweet Delight',
            'hero_subheadline' => $all['hero_subheadline'] ?? 'Explore bespoke elegance from Biba Clothings and decadent, freshly baked treats from Biba Treats. Curated with love, ordered directly on WhatsApp.',
            'whatsapp_number' => $all['whatsapp_number'] ?? '2348083271068',
            'currency_code' => $all['currency_code'] ?? 'NGN',
            'currency_symbol' => $all['currency_symbol'] ?? '₦',
            'contact_email' => $all['contact_email'] ?? 'bibaofgoodthings@gmail.com',
            'contact_phone' => $all['contact_phone'] ?? '+234 808 327 1068',
            'address' => $all['address'] ?? 'Lagos, Nigeria',
            'instagram_url' => $all['instagram_url'] ?? '',
            'facebook_url' => $all['facebook_url'] ?? '',
            'delivery_info' => $all['delivery_info'] ?? 'Nationwide delivery across Nigeria. Confectioneries are prepared fresh to order.',
            'logo_url' => !empty($all['logo_path']) ? asset($all['logo_path']) : asset('images/logo.png'),
            'logo_dark_url' => !empty($all['logo_dark_path']) ? asset($all['logo_dark_path']) : asset('images/logo_dark.png'),
        ];
    }
}
