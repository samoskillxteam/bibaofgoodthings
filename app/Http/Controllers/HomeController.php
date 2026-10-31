<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $categories = Category::where('is_active', true)
            ->withCount(['products' => function ($q) {
                $q->where('is_available', true);
            }])
            ->orderBy('display_order')
            ->get();

        $clothingCategory = $categories->firstWhere('slug', 'clothing');
        $confectioneryCategory = $categories->firstWhere('slug', 'confectionery');

        $featuredClothing = Product::where('is_available', true)
            ->where('category_id', $clothingCategory?->id)
            ->where('is_featured', true)
            ->with(['category', 'media', 'variants'])
            ->orderBy('display_order')
            ->take(4)
            ->get();

        $featuredConfectionery = Product::where('is_available', true)
            ->where('category_id', $confectioneryCategory?->id)
            ->where('is_featured', true)
            ->with(['category', 'media', 'variants'])
            ->orderBy('display_order')
            ->take(4)
            ->get();

        $newArrivals = Product::where('is_available', true)
            ->with(['category', 'media', 'variants'])
            ->latest()
            ->take(6)
            ->get();

        return Inertia::render('Home', [
            'categories' => $categories,
            'featuredClothing' => $featuredClothing,
            'featuredConfectionery' => $featuredConfectionery,
            'newArrivals' => $newArrivals,
            'aboutStory' => Setting::get('about_story'),
        ]);
    }
}
