<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        $categories = Category::withCount('products')->orderBy('display_order')->get();

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100|unique:categories,name',
            'brand_name'    => 'required|string|max:100',
            'subtitle'      => 'nullable|string|max:255',
            'description'   => 'nullable|string',
            'is_active'     => 'boolean',
            'display_order' => 'nullable|integer',
        ]);

        // Auto-generate slug from name
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);

        Category::create($validated);

        return back()->with('success', "Category '{$validated['brand_name']}' created successfully.");
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'brand_name'    => 'required|string|max:100',
            'subtitle'      => 'nullable|string|max:255',
            'description'   => 'nullable|string',
            'is_active'     => 'boolean',
            'display_order' => 'nullable|integer',
            'image'         => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $validated['image'] = $path;
        }

        $category->update($validated);

        return back()->with('success', "Category '{$category->name}' updated successfully.");
    }
}
