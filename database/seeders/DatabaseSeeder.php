<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\ProductMedia;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedUsers();
        $this->seedSettings();
        $this->seedCategoriesAndProducts();
    }

    protected function seedUsers(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@biba.com'],
            [
                'name' => 'Biba Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }

    protected function seedSettings(): void
    {
        $settings = [
            'business_name' => ['value' => 'Biba of Good Things', 'group' => 'branding', 'type' => 'text', 'desc' => 'Business Name'],
            'clothing_brand_name' => ['value' => 'Biba Clothings', 'group' => 'branding', 'type' => 'text', 'desc' => 'Clothing Brand Name'],
            'confectionery_brand_name' => ['value' => 'Biba Treats', 'group' => 'branding', 'type' => 'text', 'desc' => 'Confectionery Brand Name'],
            'tagline' => ['value' => 'Exquisite Fashion & Handcrafted Confectioneries', 'group' => 'branding', 'type' => 'text', 'desc' => 'Website Tagline'],
            'hero_headline' => ['value' => 'Where Style Meets Sweet Perfection', 'group' => 'homepage', 'type' => 'text', 'desc' => 'Homepage Hero Headline'],
            'hero_subheadline' => ['value' => 'Discover effortlessly chic attire from Biba Clothings alongside indulgent, freshly baked artisanal pastries and drinks from Biba Treats. Seamless WhatsApp checkout.', 'group' => 'homepage', 'type' => 'textarea', 'desc' => 'Homepage Subheadline'],
            'whatsapp_number' => ['value' => '2348012345678', 'group' => 'whatsapp', 'type' => 'text', 'desc' => 'WhatsApp Checkout Business Phone Number (International Format)'],
            'currency_code' => ['value' => 'NGN', 'group' => 'general', 'type' => 'text', 'desc' => 'Currency Code'],
            'currency_symbol' => ['value' => '₦', 'group' => 'general', 'type' => 'text', 'desc' => 'Currency Symbol'],
            'contact_email' => ['value' => 'orders@biba.com', 'group' => 'contact', 'type' => 'text', 'desc' => 'Contact Email'],
            'contact_phone' => ['value' => '+234 801 234 5678', 'group' => 'contact', 'type' => 'text', 'desc' => 'Contact Phone Number'],
            'address' => ['value' => 'Lekki Phase 1, Lagos, Nigeria', 'group' => 'contact', 'type' => 'text', 'desc' => 'Physical Location'],
            'instagram_url' => ['value' => 'https://instagram.com/bibagoodthings', 'group' => 'social', 'type' => 'text', 'desc' => 'Instagram URL'],
            'facebook_url' => ['value' => 'https://facebook.com/bibagoodthings', 'group' => 'social', 'type' => 'text', 'desc' => 'Facebook URL'],
            'delivery_info' => ['value' => 'Nationwide shipping available for clothing items. Fresh confectionery orders delivered same-day across Lagos.', 'group' => 'checkout', 'type' => 'textarea', 'desc' => 'Delivery and Collection Information'],
            'logo_path' => ['value' => 'images/logo.png', 'group' => 'branding', 'type' => 'image', 'desc' => 'Light Mode Logo'],
            'logo_dark_path' => ['value' => 'images/logo_dark.png', 'group' => 'branding', 'type' => 'image', 'desc' => 'Dark Mode Logo'],
            'about_story' => ['value' => 'Biba of Good Things is a curated lifestyle boutique offering two distinct passions under one roof: timeless, graceful fashion through Biba Clothings, and mouthwatering, artisanal sweet creations and beverages via Biba Treats. Every dress is stitched with care, and every cupcake is crafted with genuine passion.', 'group' => 'about', 'type' => 'textarea', 'desc' => 'About Business Story'],
        ];

        foreach ($settings as $key => $data) {
            Setting::set($key, $data['value'], $data['group'], $data['type'], $data['desc']);
        }
    }

    protected function seedCategoriesAndProducts(): void
    {
        $productsDir = storage_path('app/public/products');
        if (!File::exists($productsDir)) {
            File::makeDirectory($productsDir, 0755, true);
        }

        // 1. Clothing Category
        $clothingCat = Category::updateOrCreate(
            ['slug' => 'clothing'],
            [
                'name' => 'Clothing',
                'brand_name' => 'Biba Clothings',
                'subtitle' => 'Curated Apparel & Bespoke Tailored Elegance',
                'description' => 'Explore refined collections designed to inspire confidence. From luxurious linen sets to sweeping silk dresses and versatile everyday essentials.',
                'image' => 'products/clothing_coord.svg',
                'banner_image' => 'products/clothing_coord.svg',
                'icon' => 'Shirt',
                'is_active' => true,
                'display_order' => 1,
            ]
        );

        // 2. Confectionery Category
        $confectioneryCat = Category::updateOrCreate(
            ['slug' => 'confectionery'],
            [
                'name' => 'Confectionery',
                'brand_name' => 'Biba Treats',
                'subtitle' => 'Artisanal Cakes, Gourmet Cupcakes & Refreshing Drinks',
                'description' => 'Freshly baked indulgences crafted with premium ingredients. Perfect for celebrations, casual afternoon cravings, and memorable gift boxes.',
                'image' => 'products/treats_cupcakes.svg',
                'banner_image' => 'products/treats_cupcakes.svg',
                'icon' => 'Cake',
                'is_active' => true,
                'display_order' => 2,
            ]
        );

        // Define product list
        $clothingProducts = [
            [
                'name' => 'Linen Two-Piece Co-ord Set',
                'slug' => 'linen-two-piece-co-ord-set',
                'sku' => 'BC-LNN-001',
                'product_type' => 'clothing',
                'short_description' => 'Bespoke breathable pure linen co-ord featuring a relaxed button-down shirt and tailored wide trousers.',
                'full_description' => "Crafted from 100% premium woven linen, this two-piece set seamlessly bridges effortless daytime lounge and elevated evening gatherings.\n\n• Relaxed silhouette with camp collar\n• Elasticated waist trousers with deep side pockets\n• Pre-washed for extreme softness and minimal shrinkage\n• Designed and crafted with precision in Nigeria",
                'base_price' => 38000.00,
                'sale_price' => 35000.00,
                'cost_price' => 18000.00,
                'stock_quantity' => 25,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 1,
                'svg_filename' => 'clothing_coord.svg',
                'bg_color' => '#4A3525',
                'text_color' => '#D4B996',
                'video_index' => 1,
                'options' => [
                    'Size' => ['Small', 'Medium', 'Large', 'Extra Large'],
                    'Colour' => ['Warm Sand', 'Olive Green', 'Espresso Brown'],
                ],
                'variants' => [
                    ['title' => 'Small / Warm Sand', 'price' => 35000.00, 'sku' => 'BC-LNN-001-S-WS'],
                    ['title' => 'Medium / Warm Sand', 'price' => 35000.00, 'sku' => 'BC-LNN-001-M-WS'],
                    ['title' => 'Large / Warm Sand', 'price' => 38000.00, 'sku' => 'BC-LNN-001-L-WS'],
                    ['title' => 'Medium / Olive Green', 'price' => 35000.00, 'sku' => 'BC-LNN-001-M-OG'],
                    ['title' => 'Large / Espresso Brown', 'price' => 38000.00, 'sku' => 'BC-LNN-001-L-EB'],
                ],
                'addons' => [
                    ['name' => 'Luxury Gift Box & Ribbon', 'price' => 2500.00],
                    ['name' => 'Matching Linen Hair Scrunchie', 'price' => 1500.00],
                ],
            ],
            [
                'name' => 'Ethereal Silk Wrap Dress',
                'slug' => 'ethereal-silk-wrap-dress',
                'sku' => 'BC-SLK-002',
                'product_type' => 'clothing',
                'short_description' => 'Graceful floor-length wrap silhouette in glossy Mulberry silk blend with flutter sleeves.',
                'full_description' => "Turn heads at weddings, galas, and special dinners. The wrap contour adapts gracefully to every silhouette.\n\n• Adjustable inner and outer ties\n• Fully lined bodice\n• Breathable, lightweight hand-feel\n• Subtle side slit",
                'base_price' => 45000.00,
                'sale_price' => null,
                'cost_price' => 22000.00,
                'stock_quantity' => 15,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 2,
                'svg_filename' => 'clothing_dress.svg',
                'bg_color' => '#1E2D2F',
                'text_color' => '#C8A2C8',
                'video_index' => 2,
                'options' => [
                    'Size' => ['Small', 'Medium', 'Large'],
                    'Colour' => ['Emerald Green', 'Midnight Black', 'Blush Champagne'],
                ],
                'variants' => [
                    ['title' => 'Small / Emerald Green', 'price' => 45000.00, 'sku' => 'BC-SLK-002-S-EG'],
                    ['title' => 'Medium / Emerald Green', 'price' => 45000.00, 'sku' => 'BC-SLK-002-M-EG'],
                    ['title' => 'Large / Emerald Green', 'price' => 48000.00, 'sku' => 'BC-SLK-002-L-EG'],
                    ['title' => 'Medium / Midnight Black', 'price' => 45000.00, 'sku' => 'BC-SLK-002-M-MB'],
                ],
                'addons' => [
                    ['name' => 'Detachable Beaded Waist Belt', 'price' => 4000.00],
                ],
            ],
            [
                'name' => 'Biba Signature Oversized Cotton Tee',
                'slug' => 'biba-signature-oversized-cotton-tee',
                'sku' => 'BC-TEE-003',
                'product_type' => 'clothing',
                'short_description' => 'Heavyweight 240 GSM combed cotton t-shirt with subtle minimalist tonal embroidery.',
                'full_description' => "The ultimate staple piece. Dropped shoulders, reinforced ribbed neckband, and dense luxury cotton ensure a boxy, modern silhouette that retains its shape wash after wash.",
                'base_price' => 22000.00,
                'sale_price' => 19500.00,
                'cost_price' => 9000.00,
                'stock_quantity' => 40,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 3,
                'svg_filename' => 'clothing_tee.svg',
                'bg_color' => '#3D3A37',
                'text_color' => '#E8D8C8',
                'video_index' => 3,
                'options' => [
                    'Size' => ['Small', 'Medium', 'Large', 'Extra Large'],
                    'Colour' => ['Ivory Cream', 'Cocoa Brown', 'Sage Mist'],
                ],
                'variants' => [
                    ['title' => 'Small / Ivory Cream', 'price' => 19500.00, 'sku' => 'BC-TEE-003-S-IC'],
                    ['title' => 'Medium / Ivory Cream', 'price' => 19500.00, 'sku' => 'BC-TEE-003-M-IC'],
                    ['title' => 'Large / Cocoa Brown', 'price' => 19500.00, 'sku' => 'BC-TEE-003-L-CB'],
                    ['title' => 'Extra Large / Sage Mist', 'price' => 21000.00, 'sku' => 'BC-TEE-003-XL-SM'],
                ],
                'addons' => [],
            ],
            [
                'name' => 'Tailored Pleated Wide-Leg Trousers',
                'slug' => 'tailored-pleated-wide-leg-trousers',
                'sku' => 'BC-TRS-004',
                'product_type' => 'clothing',
                'short_description' => 'High-rise pleated front trousers in smooth stretch wool-crepe blend with clean hem drape.',
                'full_description' => "Flawless sartorial tailoring engineered for elegance and everyday versatility. Pairs effortlessly with cropped blouses or oversized blazers.",
                'base_price' => 32000.00,
                'sale_price' => null,
                'cost_price' => 15000.00,
                'stock_quantity' => 20,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => false,
                'display_order' => 4,
                'svg_filename' => 'clothing_trousers.svg',
                'bg_color' => '#2B2B2B',
                'text_color' => '#C5A880',
                'video_index' => 4,
                'options' => [
                    'Size' => ['Size 28', 'Size 30', 'Size 32', 'Size 34'],
                    'Colour' => ['Charcoal Black', 'Warm Khaki', 'Rich Chocolate'],
                ],
                'variants' => [
                    ['title' => 'Size 30 / Charcoal Black', 'price' => 32000.00, 'sku' => 'BC-TRS-004-30-CB'],
                    ['title' => 'Size 32 / Charcoal Black', 'price' => 32000.00, 'sku' => 'BC-TRS-004-32-CB'],
                    ['title' => 'Size 32 / Rich Chocolate', 'price' => 32000.00, 'sku' => 'BC-TRS-004-32-RC'],
                ],
                'addons' => [],
            ],
            [
                'name' => 'Chic Floral Maxi Kimono',
                'slug' => 'chic-floral-maxi-kimono',
                'sku' => 'BC-KMN-005',
                'product_type' => 'clothing',
                'short_description' => 'Flowing chiffon duster kimono adorned with delicate botanical floral illustrations.',
                'full_description' => "Light, dramatic, and endlessly flattering. Wear it open over swimwear or cinched over a monochrome bodysuit and denim.",
                'base_price' => 29000.00,
                'sale_price' => 26000.00,
                'cost_price' => 12000.00,
                'stock_quantity' => 18,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => false,
                'display_order' => 5,
                'svg_filename' => 'clothing_kimono.svg',
                'bg_color' => '#533B31',
                'text_color' => '#F7C59F',
                'video_index' => 5,
                'options' => [
                    'Size' => ['Standard (UK 8-14)', 'Plus (UK 16-22)'],
                    'Colour' => ['Terracotta Floral', 'Midnight Noir Floral'],
                ],
                'variants' => [
                    ['title' => 'Standard / Terracotta Floral', 'price' => 26000.00, 'sku' => 'BC-KMN-005-STD-TF'],
                    ['title' => 'Plus / Midnight Noir Floral', 'price' => 28000.00, 'sku' => 'BC-KMN-005-PLS-MN'],
                ],
                'addons' => [],
            ],
            [
                'name' => 'Structured Double-Breasted Blazer',
                'slug' => 'structured-double-breasted-blazer',
                'sku' => 'BC-BLZ-006',
                'product_type' => 'clothing',
                'short_description' => 'Sartorial power blazer featuring peak lapels, tortoise-shell horn buttons, and padded shoulders.',
                'full_description' => "Command the room with precision structure. Crafted from premium suiting fabric with silk-satin inner lining.",
                'base_price' => 48000.00,
                'sale_price' => null,
                'cost_price' => 24000.00,
                'stock_quantity' => 12,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 6,
                'svg_filename' => 'clothing_blazer.svg',
                'bg_color' => '#1F2421',
                'text_color' => '#E1CA96',
                'video_index' => 6,
                'options' => [
                    'Size' => ['Small', 'Medium', 'Large', 'Extra Large'],
                    'Colour' => ['Oatmeal Ivory', 'Onyx Black'],
                ],
                'variants' => [
                    ['title' => 'Medium / Oatmeal Ivory', 'price' => 48000.00, 'sku' => 'BC-BLZ-006-M-OI'],
                    ['title' => 'Large / Oatmeal Ivory', 'price' => 48000.00, 'sku' => 'BC-BLZ-006-L-OI'],
                    ['title' => 'Medium / Onyx Black', 'price' => 48000.00, 'sku' => 'BC-BLZ-006-M-OB'],
                ],
                'addons' => [
                    ['name' => 'Suit Protective Travel Garment Bag', 'price' => 3500.00],
                ],
            ],
        ];

        $confectioneryProducts = [
            [
                'name' => 'Assorted Luxury Cupcakes',
                'slug' => 'assorted-luxury-cupcakes',
                'sku' => 'BT-CUP-001',
                'product_type' => 'cupcake',
                'short_description' => 'Ultra-moist gourmet cupcakes crowned with silky Swiss meringue buttercream and artisan toppings.',
                'full_description' => "Baked fresh each morning using cultured butter, Madagascar bourbon vanilla, and imported Valrhona cocoa. Select your preferred pack size and signature flavours.\n\n• Fluffy sponge with melt-in-mouth crumb\n• Perfectly balanced sweetness\n• Beautifully presented in Biba Treats windowed gift boxes",
                'base_price' => 12000.00,
                'sale_price' => null,
                'cost_price' => 5500.00,
                'stock_quantity' => 50,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 1,
                'svg_filename' => 'treats_cupcakes.svg',
                'bg_color' => '#5C1D24',
                'text_color' => '#F4A8A8',
                'video_index' => 7,
                'options' => [
                    'Pack Size' => ['Box of 6', 'Box of 12', 'Box of 24'],
                    'Flavour' => ['Red Velvet Cream Cheese', 'Double Callebaut Chocolate', 'Salted Caramel Crunch', 'Madagascar Vanilla Bean'],
                ],
                'variants' => [
                    ['title' => 'Box of 6 / Red Velvet Cream Cheese', 'price' => 12000.00, 'sku' => 'BT-CUP-001-6-RV'],
                    ['title' => 'Box of 6 / Double Callebaut Chocolate', 'price' => 12000.00, 'sku' => 'BT-CUP-001-6-DC'],
                    ['title' => 'Box of 12 / Assorted Favourites', 'price' => 22000.00, 'sku' => 'BT-CUP-001-12-AST'],
                    ['title' => 'Box of 24 / Grand Celebration Box', 'price' => 40000.00, 'sku' => 'BT-CUP-001-24-GCB'],
                ],
                'addons' => [
                    ['name' => 'Custom Fondant Lettering Inscription', 'price' => 1500.00],
                    ['name' => '24K Edible Gold Leaf & Pearl Dusting', 'price' => 2500.00],
                    ['name' => 'Satin Gift Ribbon & Handwritten Card', 'price' => 1000.00],
                ],
            ],
            [
                'name' => 'Celebration Bento Cake',
                'slug' => 'celebration-bento-cake',
                'sku' => 'BT-BNT-002',
                'product_type' => 'cake',
                'short_description' => 'Charming 4-inch Korean-style bento cake customized with your special message and pastel piping.',
                'full_description' => "The trendiest way to celebrate birthdays, promotions, anniversaries, and sweet surprises. Packed neatly in an eco-friendly sugarcane bento container with a wooden fork and candle.\n\n• Feeds 1-2 persons\n• Layered with rich fruit compote or fudge cream\n• Custom text inscribed by hand",
                'base_price' => 18000.00,
                'sale_price' => 16500.00,
                'cost_price' => 7000.00,
                'stock_quantity' => 30,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 2,
                'svg_filename' => 'treats_bento.svg',
                'bg_color' => '#4A2545',
                'text_color' => '#F7CAD0',
                'video_index' => 8,
                'options' => [
                    'Flavour' => ['Strawberry Shortcake', 'Rich Chocolate Fudge', 'Cookies & Cream Vanilla'],
                    'Style' => ['Minimalist Vintage Piping', 'Floral Garden Aesthetic', 'Abstract Pastel Swirls'],
                ],
                'variants' => [
                    ['title' => 'Strawberry Shortcake / Vintage Piping', 'price' => 16500.00, 'sku' => 'BT-BNT-002-SB-VP'],
                    ['title' => 'Rich Chocolate Fudge / Abstract Swirls', 'price' => 16500.00, 'sku' => 'BT-BNT-002-CF-AS'],
                    ['title' => 'Cookies & Cream / Floral Aesthetic', 'price' => 18000.00, 'sku' => 'BT-BNT-002-CC-FA'],
                ],
                'addons' => [
                    ['name' => 'Metallic Birthday Candle Set', 'price' => 800.00],
                    ['name' => 'Acrylic Cake Topper ("Happy Birthday")', 'price' => 2000.00],
                ],
            ],
            [
                'name' => 'Gourmet Chocolate Brownie Slab',
                'slug' => 'gourmet-chocolate-brownie-slab',
                'sku' => 'BT-BRW-003',
                'product_type' => 'snack',
                'short_description' => 'Fudgy, dense Belgian chocolate brownies with crinkly paper-thin tops and pockets of molten ganache.',
                'full_description' => "Decadence redefined. Made with 70% dark chocolate and finished with Maldon sea salt flakes.",
                'base_price' => 16000.00,
                'sale_price' => null,
                'cost_price' => 7000.00,
                'stock_quantity' => 25,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => false,
                'display_order' => 3,
                'svg_filename' => 'treats_brownies.svg',
                'bg_color' => '#3D2314',
                'text_color' => '#DDB892',
                'video_index' => 9,
                'options' => [
                    'Pack Size' => ['Box of 6 Slices', 'Box of 12 Slices'],
                    'Flavour' => ['Double Fudge Sea Salt', 'Salted Caramel Pecan', 'Nutella Swirl'],
                ],
                'variants' => [
                    ['title' => 'Box of 6 / Double Fudge Sea Salt', 'price' => 16000.00, 'sku' => 'BT-BRW-003-6-DF'],
                    ['title' => 'Box of 12 / Salted Caramel Pecan', 'price' => 28000.00, 'sku' => 'BT-BRW-003-12-SC'],
                ],
                'addons' => [
                    ['name' => 'Warm Caramel Dipping Pot', 'price' => 1500.00],
                ],
            ],
            [
                'name' => 'Handcrafted Fresh Pastry Box',
                'slug' => 'handcrafted-fresh-pastry-box',
                'sku' => 'BT-PST-004',
                'product_type' => 'pastry',
                'short_description' => 'Golden buttery croissants, pain au chocolat, and seasonal berry danishes made with French butter.',
                'full_description' => "Laminated over 72 hours for over 27 crispy, airy layers. The ultimate breakfast or brunch centerpiece.",
                'base_price' => 15000.00,
                'sale_price' => 14000.00,
                'cost_price' => 6000.00,
                'stock_quantity' => 20,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 4,
                'svg_filename' => 'treats_pastries.svg',
                'bg_color' => '#704214',
                'text_color' => '#FFE6A7',
                'video_index' => 10,
                'options' => [
                    'Selection' => ['Classic French Mix (6 pcs)', 'All Chocolate Lover (6 pcs)', 'Grand Family Platter (12 pcs)'],
                ],
                'variants' => [
                    ['title' => 'Classic French Mix (6 pcs)', 'price' => 14000.00, 'sku' => 'BT-PST-004-CFM'],
                    ['title' => 'All Chocolate Lover (6 pcs)', 'price' => 15000.00, 'sku' => 'BT-PST-004-ACL'],
                    ['title' => 'Grand Family Platter (12 pcs)', 'price' => 26000.00, 'sku' => 'BT-PST-004-GFP'],
                ],
                'addons' => [],
            ],
            [
                'name' => 'Sparkling Hibiscus & Fruit Refresher',
                'slug' => 'sparkling-hibiscus-fruit-refresher',
                'sku' => 'BT-DRK-005',
                'product_type' => 'drink',
                'short_description' => 'Botanical artisanal zobo infused with crisp apple, orange blossom, fresh mint, and mild sparkling fizz.',
                'full_description' => "Our signature cold-brewed hibiscus drink. Naturally packed with antioxidants, delightfully tart and sweetened with natural fruit essences.",
                'base_price' => 4500.00,
                'sale_price' => null,
                'cost_price' => 1500.00,
                'stock_quantity' => 60,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => false,
                'display_order' => 5,
                'svg_filename' => 'treats_drink.svg',
                'bg_color' => '#8B0000',
                'text_color' => '#FF9999',
                'video_index' => 11,
                'options' => [
                    'Bottle Size' => ['500ml Glass Bottle', '1-Litre Family Pitcher', 'Pack of 6 (500ml)'],
                ],
                'variants' => [
                    ['title' => '500ml Glass Bottle', 'price' => 4500.00, 'sku' => 'BT-DRK-005-500ML'],
                    ['title' => '1-Litre Family Pitcher', 'price' => 8000.00, 'sku' => 'BT-DRK-005-1L'],
                    ['title' => 'Pack of 6 (500ml)', 'price' => 24000.00, 'sku' => 'BT-DRK-005-PK6'],
                ],
                'addons' => [],
            ],
            [
                'name' => 'Velvety Strawberry Cheesecake Box',
                'slug' => 'velvety-strawberry-cheesecake-box',
                'sku' => 'BT-CHK-006',
                'product_type' => 'cake',
                'short_description' => 'Dense baked Philadelphia cream cheesecake resting on a buttery honey-graham biscuit crust with strawberry coulis.',
                'full_description' => "Slow-baked in a water bath to achieve a cloud-like creaminess without browning. Topped with fresh macerated strawberries.",
                'base_price' => 14000.00,
                'sale_price' => null,
                'cost_price' => 6000.00,
                'stock_quantity' => 25,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 6,
                'svg_filename' => 'treats_cheesecake.svg',
                'bg_color' => '#6B2D5C',
                'text_color' => '#FFD6E0',
                'video_index' => 12,
                'options' => [
                    'Portion' => ['Duo Box (2 Slices)', 'Quartet Box (4 Slices)', 'Whole 8-inch Cake'],
                ],
                'variants' => [
                    ['title' => 'Duo Box (2 Slices)', 'price' => 14000.00, 'sku' => 'BT-CHK-006-2S'],
                    ['title' => 'Quartet Box (4 Slices)', 'price' => 26000.00, 'sku' => 'BT-CHK-006-4S'],
                    ['title' => 'Whole 8-inch Cake', 'price' => 45000.00, 'sku' => 'BT-CHK-006-8IN'],
                ],
                'addons' => [
                    ['name' => 'Extra Strawberry Coulis Jar (100ml)', 'price' => 1500.00],
                ],
            ],
        ];

        // Seed Clothing Products
        $this->createProductsWithMedia($clothingCat, $clothingProducts);

        // Seed Confectionery Products
        $this->createProductsWithMedia($confectioneryCat, $confectioneryProducts);
    }

    protected function createProductsWithMedia(Category $category, array $products): void
    {
        $productsDir = storage_path('app/public/products');

        foreach ($products as $pData) {
            // 1. Generate SVG image
            $svgPath = $productsDir . '/' . $pData['svg_filename'];
            $svgContent = $this->generateProductSvg(
                $pData['name'],
                $category->brand_name,
                $pData['short_description'],
                $pData['bg_color'],
                $pData['text_color']
            );
            File::put($svgPath, $svgContent);

            // 2. Create or Update Product
            $product = Product::updateOrCreate(
                ['slug' => $pData['slug']],
                [
                    'category_id' => $category->id,
                    'name' => $pData['name'],
                    'sku' => $pData['sku'],
                    'product_type' => $pData['product_type'],
                    'short_description' => $pData['short_description'],
                    'full_description' => $pData['full_description'],
                    'base_price' => $pData['base_price'],
                    'sale_price' => $pData['sale_price'],
                    'cost_price' => $pData['cost_price'],
                    'stock_quantity' => $pData['stock_quantity'],
                    'track_stock' => $pData['track_stock'],
                    'is_available' => $pData['is_available'],
                    'is_featured' => $pData['is_featured'],
                    'display_order' => $pData['display_order'],
                    'meta_title' => $pData['name'] . ' | ' . $category->brand_name,
                    'meta_description' => $pData['short_description'],
                ]
            );

            // 3. Clear old media and attach primary image
            $product->media()->delete();

            ProductMedia::create([
                'product_id' => $product->id,
                'file_path' => 'products/' . $pData['svg_filename'],
                'media_type' => 'image',
                'alt_text' => $pData['name'],
                'display_order' => 1,
                'is_primary' => true,
            ]);

            // 4. Attach Video if available
            $videoIndex = $pData['video_index'] ?? null;
            if ($videoIndex) {
                $videoFilename = "product_video_{$videoIndex}.mp4";
                $videoFullPath = $productsDir . '/' . $videoFilename;
                if (File::exists($videoFullPath)) {
                    ProductMedia::create([
                        'product_id' => $product->id,
                        'file_path' => 'products/' . $videoFilename,
                        'media_type' => 'video',
                        'alt_text' => $pData['name'] . ' Showcase Video',
                        'display_order' => 2,
                        'is_primary' => false,
                    ]);
                }
            }

            // 5. Create Options and Values
            $product->options()->delete();
            $optionMap = [];
            $displayOrder = 1;
            foreach ($pData['options'] as $optName => $values) {
                $option = ProductOption::create([
                    'product_id' => $product->id,
                    'name' => $optName,
                    'display_order' => $displayOrder++,
                ]);

                $valOrder = 1;
                foreach ($values as $valName) {
                    $optValue = ProductOptionValue::create([
                        'product_option_id' => $option->id,
                        'name' => $valName,
                        'display_order' => $valOrder++,
                    ]);
                    $optionMap[$optName][$valName] = $optValue->id;
                }
            }

            // 6. Create Variants
            $product->variants()->delete();
            foreach ($pData['variants'] as $vData) {
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $vData['sku'] ?? null,
                    'title' => $vData['title'],
                    'price' => $vData['price'],
                    'sale_price' => $vData['sale_price'] ?? null,
                    'stock_quantity' => 20,
                    'is_available' => true,
                ]);

                // Map option values to variant if names match
                $parts = explode(' / ', $vData['title']);
                $valIds = [];
                foreach ($parts as $part) {
                    $trimPart = trim($part);
                    foreach ($optionMap as $optValues) {
                        if (isset($optValues[$trimPart])) {
                            $valIds[] = $optValues[$trimPart];
                        }
                    }
                }
                if (!empty($valIds)) {
                    $variant->optionValues()->sync($valIds);
                }
            }

            // 7. Create Addons
            $product->addons()->delete();
            $addonOrder = 1;
            foreach ($pData['addons'] as $aData) {
                ProductAddon::create([
                    'product_id' => $product->id,
                    'name' => $aData['name'],
                    'price' => $aData['price'],
                    'is_available' => true,
                    'display_order' => $addonOrder++,
                ]);
            }
        }
    }

    protected function generateProductSvg(string $title, string $brand, string $shortDesc, string $bg, string $accent): string
    {
        $escapedTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $escapedBrand = htmlspecialchars($brand, ENT_QUOTES, 'UTF-8');
        $escapedDesc = htmlspecialchars(Str::limit($shortDesc, 60), ENT_QUOTES, 'UTF-8');

        $logoPath = public_path('images/logo.png');
        $logoDataUri = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : '';

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 1000" width="800" height="1000">
  <defs>
    <radialGradient id="rad" cx="50%" cy="40%" r="60%">
      <stop offset="0%" stop-color="{$bg}" stop-opacity="0.9" />
      <stop offset="100%" stop-color="#141416" />
    </radialGradient>
    <linearGradient id="goldGlow" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="{$accent}" />
      <stop offset="100%" stop-color="#E2D4B7" />
    </linearGradient>
  </defs>
  <rect width="800" height="1000" fill="url(#rad)" />
  <rect x="30" y="30" width="740" height="940" fill="none" stroke="{$accent}" stroke-width="1" opacity="0.3" rx="16" />
  <circle cx="400" cy="400" r="270" fill="none" stroke="{$accent}" stroke-width="1.5" stroke-dasharray="4 8" opacity="0.25" />
  <circle cx="400" cy="400" r="195" fill="none" stroke="{$accent}" stroke-width="1" opacity="0.15" />
  
  <g transform="translate(400, 390)">
    <circle cx="0" cy="0" r="165" fill="{$accent}" opacity="0.1" />
    <image href="{$logoDataUri}" x="-140" y="-140" width="280" height="280" preserveAspectRatio="xMidYMid meet" />
  </g>

  <text x="400" y="140" text-anchor="middle" fill="{$accent}" font-family="'Inter', sans-serif" font-size="16" letter-spacing="8" font-weight="600">{$escapedBrand}</text>
  <text x="400" y="650" text-anchor="middle" fill="#FFFFFF" font-family="'Cinzel', 'Playfair Display', Georgia, serif" font-size="34" font-weight="700">{$escapedTitle}</text>
  <text x="400" y="700" text-anchor="middle" fill="{$accent}" font-family="'Inter', sans-serif" font-size="18" font-weight="400" opacity="0.85">{$escapedDesc}</text>
  <text x="400" y="870" text-anchor="middle" fill="#A1A1AA" font-family="'Inter', sans-serif" font-size="13" letter-spacing="4" font-weight="500">EXQUISITE CRAFTSMANSHIP • WHATSAPP EXCLUSIVE</text>
</svg>
SVG;
    }
}
