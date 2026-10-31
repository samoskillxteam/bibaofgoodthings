<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\ProductMedia;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Product::with(['category', 'media', 'variants']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($request->has('is_available') && $request->input('is_available') !== '') {
            $query->where('is_available', $request->boolean('is_available'));
        }

        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'name_asc' => $query->orderBy('name', 'asc'),
            'price_asc' => $query->orderBy('base_price', 'asc'),
            'price_desc' => $query->orderBy('base_price', 'desc'),
            'featured' => $query->orderByDesc('is_featured')->orderBy('display_order'),
            default => $query->latest(),
        };

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get(['id', 'name', 'brand_name']);

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category_id', 'is_available', 'sort']),
        ]);
    }

    public function create(): Response
    {
        $categories = Category::where('is_active', true)->orderBy('display_order')->get();

        return Inertia::render('Admin/Products/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'track_stock' => $request->boolean('track_stock'),
            'is_available' => $request->boolean('is_available', true),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        if ($request->has('variants') && is_array($request->input('variants'))) {
            $variants = $request->input('variants');
            foreach ($variants as $k => $v) {
                if (isset($v['is_available'])) {
                    $variants[$k]['is_available'] = filter_var($v['is_available'], FILTER_VALIDATE_BOOLEAN);
                }
            }
            $request->merge(['variants' => $variants]);
        }

        if ($request->has('addons') && is_array($request->input('addons'))) {
            $addons = $request->input('addons');
            foreach ($addons as $k => $a) {
                if (isset($a['is_available'])) {
                    $addons[$k]['is_available'] = filter_var($a['is_available'], FILTER_VALIDATE_BOOLEAN);
                }
            }
            $request->merge(['addons' => $addons]);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'product_type' => 'nullable|string|max:50',
            'short_description' => 'nullable|string|max:500',
            'full_description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'track_stock' => 'boolean',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'display_order' => 'nullable|integer',
            'images.*' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg|max:10240',
            'videos.*' => 'nullable|file|mimes:mp4,mov,webm|max:51200',
            'variants' => 'nullable|array',
            'variants.*.title' => 'required|string|max:255',
            'variants.*.sku' => 'nullable|string|max:100',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.sale_price' => 'nullable|numeric|min:0',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
            'addons' => 'nullable|array',
            'addons.*.name' => 'required|string|max:255',
            'addons.*.price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Generate unique slug
            $slug = Str::slug($validated['name']);
            $count = Product::where('slug', 'like', "{$slug}%")->count();
            if ($count > 0) {
                $slug .= '-' . ($count + 1);
            }

            $product = Product::create([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'slug' => $slug,
                'sku' => $validated['sku'] ?? null,
                'product_type' => $validated['product_type'] ?? null,
                'short_description' => $validated['short_description'] ?? null,
                'full_description' => $validated['full_description'] ?? null,
                'base_price' => $validated['base_price'],
                'sale_price' => $validated['sale_price'] ?? null,
                'cost_price' => $validated['cost_price'] ?? null,
                'stock_quantity' => $validated['stock_quantity'] ?? 0,
                'track_stock' => $request->boolean('track_stock'),
                'is_available' => $request->boolean('is_available', true),
                'is_featured' => $request->boolean('is_featured'),
                'display_order' => $validated['display_order'] ?? 0,
            ]);

            // Handle Image Uploads
            if ($request->hasFile('images')) {
                $isFirst = true;
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    ProductMedia::create([
                        'product_id' => $product->id,
                        'file_path' => $path,
                        'media_type' => 'image',
                        'alt_text' => $product->name,
                        'display_order' => 1,
                        'is_primary' => $isFirst,
                        'file_size' => $image->getSize(),
                    ]);
                    $isFirst = false;
                }
            }

            // Handle Video Uploads
            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $video) {
                    $path = $video->store('products', 'public');
                    ProductMedia::create([
                        'product_id' => $product->id,
                        'file_path' => $path,
                        'media_type' => 'video',
                        'alt_text' => $product->name . ' Video',
                        'display_order' => 2,
                        'is_primary' => false,
                        'file_size' => $video->getSize(),
                    ]);
                }
            }

            // Create Variants
            if (!empty($validated['variants'])) {
                foreach ($validated['variants'] as $v) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'title' => $v['title'],
                        'sku' => $v['sku'] ?? null,
                        'price' => $v['price'],
                        'sale_price' => $v['sale_price'] ?? null,
                        'stock_quantity' => $v['stock_quantity'] ?? 0,
                        'is_available' => true,
                    ]);
                }
            }

            // Create Addons
            if (!empty($validated['addons'])) {
                foreach ($validated['addons'] as $a) {
                    ProductAddon::create([
                        'product_id' => $product->id,
                        'name' => $a['name'],
                        'price' => $a['price'],
                        'is_available' => true,
                    ]);
                }
            }
        });

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    public function edit(Product $product): Response
    {
        $product->load([
            'category',
            'media',
            'options.values',
            'variants',
            'addons',
        ]);

        $categories = Category::where('is_active', true)->orderBy('display_order')->get();

        return Inertia::render('Admin/Products/Edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->merge([
            'track_stock' => $request->boolean('track_stock'),
            'is_available' => $request->boolean('is_available', true),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        if ($request->has('variants') && is_array($request->input('variants'))) {
            $variants = $request->input('variants');
            foreach ($variants as $k => $v) {
                if (isset($v['is_available'])) {
                    $variants[$k]['is_available'] = filter_var($v['is_available'], FILTER_VALIDATE_BOOLEAN);
                }
            }
            $request->merge(['variants' => $variants]);
        }

        if ($request->has('addons') && is_array($request->input('addons'))) {
            $addons = $request->input('addons');
            foreach ($addons as $k => $a) {
                if (isset($a['is_available'])) {
                    $addons[$k]['is_available'] = filter_var($a['is_available'], FILTER_VALIDATE_BOOLEAN);
                }
            }
            $request->merge(['addons' => $addons]);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'product_type' => 'nullable|string|max:50',
            'short_description' => 'nullable|string|max:500',
            'full_description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'track_stock' => 'boolean',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'display_order' => 'nullable|integer',
            'images.*' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg|max:10240',
            'videos.*' => 'nullable|file|mimes:mp4,mov,webm|max:51200',
            'remove_media_ids' => 'nullable|array',
            'remove_media_ids.*' => 'integer|exists:product_media,id',
            'primary_media_id' => 'nullable|integer|exists:product_media,id',
            'options' => 'nullable|array',
            'options.*.id' => 'nullable|integer',
            'options.*.name' => 'required|string|max:255',
            'options.*.display_order' => 'nullable|integer',
            'options.*.values' => 'nullable|array',
            'options.*.values.*.id' => 'nullable|integer',
            'options.*.values.*.name' => 'required|string|max:255',
            'options.*.values.*.price_modifier' => 'nullable|numeric',
            'options.*.values.*.color_code' => 'nullable|string|max:50',
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|integer',
            'variants.*.title' => 'required|string|max:255',
            'variants.*.sku' => 'nullable|string|max:100',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.sale_price' => 'nullable|numeric|min:0',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
            'variants.*.is_available' => 'boolean',
            'addons' => 'nullable|array',
            'addons.*.id' => 'nullable|integer',
            'addons.*.name' => 'required|string|max:255',
            'addons.*.price' => 'required|numeric|min:0',
            'addons.*.is_available' => 'boolean',
        ]);

        DB::transaction(function () use ($validated, $request, $product) {
            $product->update([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'sku' => $validated['sku'] ?? null,
                'product_type' => $validated['product_type'] ?? null,
                'short_description' => $validated['short_description'] ?? null,
                'full_description' => $validated['full_description'] ?? null,
                'base_price' => $validated['base_price'],
                'sale_price' => $validated['sale_price'] ?? null,
                'cost_price' => $validated['cost_price'] ?? null,
                'stock_quantity' => $validated['stock_quantity'] ?? 0,
                'track_stock' => $request->boolean('track_stock'),
                'is_available' => $request->boolean('is_available', true),
                'is_featured' => $request->boolean('is_featured'),
                'display_order' => $validated['display_order'] ?? 0,
            ]);

            // Remove selected media
            if (!empty($validated['remove_media_ids'])) {
                $mediasToDelete = ProductMedia::where('product_id', $product->id)
                    ->whereIn('id', $validated['remove_media_ids'])
                    ->get();
                foreach ($mediasToDelete as $m) {
                    Storage::disk('public')->delete($m->file_path);
                    $m->delete();
                }
            }

            // Handle New Image Uploads
            if ($request->hasFile('images')) {
                $hasPrimary = $product->media()->where('media_type', 'image')->where('is_primary', true)->exists();
                $isFirst = !$hasPrimary;
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    ProductMedia::create([
                        'product_id' => $product->id,
                        'file_path' => $path,
                        'media_type' => 'image',
                        'alt_text' => $product->name,
                        'display_order' => 1,
                        'is_primary' => $isFirst,
                        'file_size' => $image->getSize(),
                    ]);
                    $isFirst = false;
                }
            }

            // Handle New Video Uploads
            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $video) {
                    $path = $video->store('products', 'public');
                    ProductMedia::create([
                        'product_id' => $product->id,
                        'file_path' => $path,
                        'media_type' => 'video',
                        'alt_text' => $product->name . ' Video',
                        'display_order' => 2,
                        'is_primary' => false,
                        'file_size' => $video->getSize(),
                    ]);
                }
            }

            // Update primary media
            if (!empty($validated['primary_media_id'])) {
                ProductMedia::where('product_id', $product->id)->update(['is_primary' => false]);
                ProductMedia::where('product_id', $product->id)
                    ->where('id', $validated['primary_media_id'])
                    ->update(['is_primary' => true]);
            } else {
                // Ensure there is at least one primary image if images exist
                $hasPrimary = $product->media()->where('media_type', 'image')->where('is_primary', true)->exists();
                if (!$hasPrimary) {
                    $firstImg = $product->media()->where('media_type', 'image')->first();
                    if ($firstImg) {
                        $firstImg->update(['is_primary' => true]);
                    }
                }
            }

            // Sync Options & Option Values
            if (!empty($validated['options'])) {
                $submittedOptionIds = [];
                foreach ($validated['options'] as $idx => $opt) {
                    $option = ProductOption::updateOrCreate(
                        [
                            'id' => $opt['id'] ?? null,
                            'product_id' => $product->id,
                        ],
                        [
                            'name' => $opt['name'],
                            'display_order' => $opt['display_order'] ?? $idx,
                        ]
                    );
                    $submittedOptionIds[] = $option->id;

                    $submittedValIds = [];
                    if (!empty($opt['values'])) {
                        foreach ($opt['values'] as $vIdx => $val) {
                            if (empty($val['name'])) continue;
                            $value = ProductOptionValue::updateOrCreate(
                                [
                                    'id' => $val['id'] ?? null,
                                    'product_option_id' => $option->id,
                                ],
                                [
                                    'name' => $val['name'],
                                    'price_modifier' => $val['price_modifier'] ?? 0,
                                    'color_code' => $val['color_code'] ?? null,
                                    'display_order' => $vIdx,
                                ]
                            );
                            $submittedValIds[] = $value->id;
                        }
                    }
                    ProductOptionValue::where('product_option_id', $option->id)
                        ->whereNotIn('id', $submittedValIds)
                        ->delete();
                }
                ProductOption::where('product_id', $product->id)
                    ->whereNotIn('id', $submittedOptionIds)
                    ->delete();
            }

            // Sync Variants
            $submittedVariantIds = [];
            if (!empty($validated['variants'])) {
                foreach ($validated['variants'] as $v) {
                    $variant = ProductVariant::updateOrCreate(
                        [
                            'id' => $v['id'] ?? null,
                            'product_id' => $product->id,
                        ],
                        [
                            'title' => $v['title'],
                            'sku' => $v['sku'] ?? null,
                            'price' => $v['price'],
                            'sale_price' => $v['sale_price'] ?? null,
                            'stock_quantity' => $v['stock_quantity'] ?? 0,
                            'is_available' => $v['is_available'] ?? true,
                        ]
                    );
                    $submittedVariantIds[] = $variant->id;
                }
            }
            ProductVariant::where('product_id', $product->id)
                ->whereNotIn('id', $submittedVariantIds)
                ->delete();

            // Sync Addons
            $submittedAddonIds = [];
            if (!empty($validated['addons'])) {
                foreach ($validated['addons'] as $a) {
                    $addon = ProductAddon::updateOrCreate(
                        [
                            'id' => $a['id'] ?? null,
                            'product_id' => $product->id,
                        ],
                        [
                            'name' => $a['name'],
                            'price' => $a['price'],
                            'is_available' => $a['is_available'] ?? true,
                        ]
                    );
                    $submittedAddonIds[] = $addon->id;
                }
            }
            ProductAddon::where('product_id', $product->id)
                ->whereNotIn('id', $submittedAddonIds)
                ->delete();
        });

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('info', "Product '{$product->name}' was deleted.");
    }

    public function toggleAvailability(Product $product): RedirectResponse
    {
        $product->update(['is_available' => !$product->is_available]);

        return back()->with('success', "Updated availability for {$product->name}.");
    }

    public function toggleFeatured(Product $product): RedirectResponse
    {
        $product->update(['is_featured' => !$product->is_featured]);

        return back()->with('success', "Updated featured status for {$product->name}.");
    }
}
