<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected const SESSION_KEY = 'biba_cart';

    public function getRawCart(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public function add(int $productId, int $quantity = 1, ?int $variantId = null, array $addonIds = [], ?string $notes = null): array
    {
        $quantity = max(1, $quantity);
        sort($addonIds);
        $itemKey = $this->generateItemKey($productId, $variantId, $addonIds, $notes);

        $cart = $this->getRawCart();

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantity'] += $quantity;
        } else {
            $cart[$itemKey] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'addon_ids' => $addonIds,
                'notes' => $notes ? trim($notes) : null,
                'quantity' => $quantity,
                'added_at' => now()->toIso8601String(),
            ];
        }

        Session::put(self::SESSION_KEY, $cart);

        return $this->getValidatedCart();
    }

    public function update(string $itemKey, int $quantity): array
    {
        $cart = $this->getRawCart();

        if (isset($cart[$itemKey])) {
            if ($quantity <= 0) {
                unset($cart[$itemKey]);
            } else {
                $cart[$itemKey]['quantity'] = $quantity;
            }
            Session::put(self::SESSION_KEY, $cart);
        }

        return $this->getValidatedCart();
    }

    public function remove(string $itemKey): array
    {
        $cart = $this->getRawCart();

        if (isset($cart[$itemKey])) {
            unset($cart[$itemKey]);
            Session::put(self::SESSION_KEY, $cart);
        }

        return $this->getValidatedCart();
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function getValidatedCart(): array
    {
        $rawCart = $this->getRawCart();
        $items = [];
        $subtotal = 0.0;
        $totalQuantity = 0;
        $warnings = [];

        if (empty($rawCart)) {
            return [
                'items' => [],
                'total_quantity' => 0,
                'subtotal' => 0.0,
                'formatted_subtotal' => '₦0',
                'is_empty' => true,
                'warnings' => [],
            ];
        }

        $productIds = array_unique(array_column($rawCart, 'product_id'));
        $products = Product::with(['category', 'media', 'variants.optionValues', 'addons'])
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $updatedCart = [];

        foreach ($rawCart as $key => $item) {
            $productId = $item['product_id'];
            $product = $products->get($productId);

            if (!$product || !$product->is_available) {
                $warnings[] = "A product in your cart is currently unavailable and was removed.";
                continue;
            }

            $variant = null;
            if (!empty($item['variant_id'])) {
                $variant = $product->variants->firstWhere('id', $item['variant_id']);
                if (!$variant || !$variant->is_available) {
                    $warnings[] = "Selected variation for '{$product->name}' is no longer available.";
                    continue;
                }
            } elseif ($product->variants->isNotEmpty()) {
                // If product requires variants but none was selected, auto-select first available variant
                $variant = $product->variants->firstWhere('is_available', true);
            }

            // Calculate unit price from authoritative server database
            $basePrice = $variant ? $variant->effective_price : $product->effective_price;

            $validAddons = [];
            $addonsPrice = 0.0;
            if (!empty($item['addon_ids'])) {
                foreach ($item['addon_ids'] as $addonId) {
                    $addon = $product->addons->firstWhere('id', $addonId);
                    if ($addon && $addon->is_available) {
                        $validAddons[] = [
                            'id' => $addon->id,
                            'name' => $addon->name,
                            'price' => (float) $addon->price,
                            'formatted_price' => '₦' . number_format((float) $addon->price, 0),
                        ];
                        $addonsPrice += (float) $addon->price;
                    }
                }
            }

            $unitPrice = $basePrice + $addonsPrice;
            $quantity = max(1, (int) $item['quantity']);
            $lineTotal = $unitPrice * $quantity;

            $subtotal += $lineTotal;
            $totalQuantity += $quantity;

            $items[] = [
                'key' => $key,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_slug' => $product->slug,
                'category_name' => $product->category->name ?? 'General',
                'category_slug' => $product->category->slug ?? 'clothing',
                'brand_name' => $product->category->brand_name ?? 'Biba',
                'image_url' => $product->primary_image_url,
                'variant_id' => $variant ? $variant->id : null,
                'variant_title' => $variant ? $variant->title : null,
                'selected_options' => $variant ? $variant->optionValues->pluck('name', 'option.name')->toArray() : [],
                'addons' => $validAddons,
                'notes' => $item['notes'] ?? null,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'formatted_unit_price' => '₦' . number_format($unitPrice, 0),
                'line_total' => $lineTotal,
                'formatted_line_total' => '₦' . number_format($lineTotal, 0),
            ];

            // Retain valid item in session
            $updatedCart[$key] = [
                'product_id' => $product->id,
                'variant_id' => $variant ? $variant->id : null,
                'addon_ids' => array_column($validAddons, 'id'),
                'notes' => $item['notes'] ?? null,
                'quantity' => $quantity,
                'added_at' => $item['added_at'] ?? now()->toIso8601String(),
            ];
        }

        Session::put(self::SESSION_KEY, $updatedCart);

        return [
            'items' => $items,
            'total_quantity' => $totalQuantity,
            'subtotal' => $subtotal,
            'formatted_subtotal' => '₦' . number_format($subtotal, 0),
            'is_empty' => count($items) === 0,
            'warnings' => array_unique($warnings),
        ];
    }

    protected function generateItemKey(int $productId, ?int $variantId, array $addonIds, ?string $notes): string
    {
        return md5("{$productId}_{$variantId}_" . implode(',', $addonIds) . "_" . ($notes ?? ''));
    }
}
