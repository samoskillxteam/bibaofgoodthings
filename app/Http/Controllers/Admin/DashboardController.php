<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrderEnquiry;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $clothingCat = Category::where('slug', 'clothing')->first();
        $confectioneryCat = Category::where('slug', 'confectionery')->first();

        $stats = [
            'total_products' => Product::count(),
            'clothing_count' => Product::where('category_id', $clothingCat?->id)->count(),
            'confectionery_count' => Product::where('category_id', $confectioneryCat?->id)->count(),
            'published_count' => Product::where('is_available', true)->count(),
            'unavailable_count' => Product::where('is_available', false)->count(),
            'low_stock_count' => Product::where('track_stock', true)->where('stock_quantity', '<=', 5)->count(),
            'total_enquiries' => OrderEnquiry::count(),
            'pending_enquiries' => OrderEnquiry::where('status', 'pending')->count(),
        ];

        $recentEnquiries = OrderEnquiry::latest()->take(5)->get();
        $recentProducts = Product::with(['category', 'media'])->latest()->take(6)->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentEnquiries' => $recentEnquiries,
            'recentProducts' => $recentProducts,
        ]);
    }
}
