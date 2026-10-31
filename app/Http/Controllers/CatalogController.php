<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function clothing(Request $request): Response
    {
        $category = Category::where('slug', 'clothing')->firstOrFail();

        $query = Product::where('category_id', $category->id)
            ->where('is_available', true)
            ->with(['category', 'media', 'variants']);

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->input('sort', 'featured');
        match ($sort) {
            'price_asc' => $query->orderBy('base_price', 'asc'),
            'price_desc' => $query->orderBy('base_price', 'desc'),
            'newest' => $query->latest(),
            default => $query->orderByDesc('is_featured')->orderBy('display_order'),
        };

        $products = $query->paginate(12)->withQueryString();

        return Inertia::render('Clothing/Index', [
            'category' => $category,
            'products' => $products,
            'filters' => $request->only(['search', 'sort']),
        ]);
    }

    public function clothingShow(string $slug): Response
    {
        $product = Product::where('slug', $slug)
            ->where('is_available', true)
            ->with([
                'category',
                'media',
                'options.values',
                'variants.optionValues',
                'addons' => function ($q) {
                    $q->where('is_available', true)->orderBy('display_order');
                },
            ])
            ->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_available', true)
            ->with(['category', 'media'])
            ->take(4)
            ->get();

        return Inertia::render('Clothing/Show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }

    public function confectionery(Request $request): Response
    {
        $category = Category::where('slug', 'confectionery')->firstOrFail();

        $query = Product::where('category_id', $category->id)
            ->where('is_available', true)
            ->with(['category', 'media', 'variants']);

        // Filter by product type (cupcake, cake, drink, snack, pastry)
        if ($type = $request->input('type')) {
            $query->where('product_type', $type);
        }

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->input('sort', 'featured');
        match ($sort) {
            'price_asc' => $query->orderBy('base_price', 'asc'),
            'price_desc' => $query->orderBy('base_price', 'desc'),
            'newest' => $query->latest(),
            default => $query->orderByDesc('is_featured')->orderBy('display_order'),
        };

        $products = $query->paginate(12)->withQueryString();

        $productTypes = Product::where('category_id', $category->id)
            ->whereNotNull('product_type')
            ->distinct()
            ->pluck('product_type');

        return Inertia::render('Confectionery/Index', [
            'category' => $category,
            'products' => $products,
            'productTypes' => $productTypes,
            'filters' => $request->only(['search', 'type', 'sort']),
        ]);
    }

    public function confectioneryShow(string $slug): Response
    {
        $product = Product::where('slug', $slug)
            ->where('is_available', true)
            ->with([
                'category',
                'media',
                'options.values',
                'variants.optionValues',
                'addons' => function ($q) {
                    $q->where('is_available', true)->orderBy('display_order');
                },
            ])
            ->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_available', true)
            ->with(['category', 'media'])
            ->take(4)
            ->get();

        return Inertia::render('Confectionery/Show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
