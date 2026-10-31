<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderEnquiryController as AdminOrderEnquiryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\WhatsAppCheckoutController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Biba of Good Things (Clothings & Treats)
|--------------------------------------------------------------------------
*/

// Public Storefront Pages
Route::get('/', [HomeController::class, 'index'])->name('home');

// Clothing Catalog
Route::get('/clothing', [CatalogController::class, 'clothing'])->name('clothing.index');
Route::get('/clothing/{slug}', [CatalogController::class, 'clothingShow'])->name('clothing.show');

// Treats Catalog (formerly /confectionery)
Route::get('/treats', [CatalogController::class, 'confectionery'])->name('confectionery.index');
Route::get('/treats/{slug}', [CatalogController::class, 'confectioneryShow'])->name('confectionery.show');
// Legacy redirect
Route::redirect('/confectionery', '/treats', 301);
Route::redirect('/confectionery/{slug}', '/treats/{slug}', 301);

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{itemKey}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{itemKey}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Exclusive WhatsApp Checkout
Route::post('/checkout/whatsapp', [WhatsAppCheckoutController::class, 'checkout'])->name('checkout.whatsapp');

// Static Pages
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');

// Admin Authentication
Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');

Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'create'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'store'])->name('admin.login.store');
    Route::post('/logout', [AdminAuthController::class, 'destroy'])->name('admin.logout');

    // Protected Admin Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // Products Management
        Route::get('/products', [AdminProductController::class, 'index'])->name('admin.products.index');
        Route::get('/products/create', [AdminProductController::class, 'create'])->name('admin.products.create');
        Route::post('/products', [AdminProductController::class, 'store'])->name('admin.products.store');
        Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('admin.products.edit');
        Route::match(['put', 'patch', 'post'], '/products/{product}', [AdminProductController::class, 'update'])->name('admin.products.update');
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');
        Route::post('/products/{product}/toggle-availability', [AdminProductController::class, 'toggleAvailability'])->name('admin.products.toggle-availability');
        Route::post('/products/{product}/toggle-featured', [AdminProductController::class, 'toggleFeatured'])->name('admin.products.toggle-featured');

        // Categories Management
        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
        Route::match(['put', 'patch', 'post'], '/categories/{category}', [AdminCategoryController::class, 'update'])->name('admin.categories.update');

        // Order Enquiries
        Route::get('/orders', [AdminOrderEnquiryController::class, 'index'])->name('admin.orders.index');
        Route::get('/orders/{enquiry}', [AdminOrderEnquiryController::class, 'show'])->name('admin.orders.show');
        Route::patch('/orders/{enquiry}/status', [AdminOrderEnquiryController::class, 'updateStatus'])->name('admin.orders.status');

        // Settings Management
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('admin.settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('admin.settings.update');
    });
});
