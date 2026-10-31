<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index(): Response
    {
        $cart = $this->cartService->getValidatedCart();

        return Inertia::render('Cart/Index', [
            'cart' => $cart,
            'whatsappNumber' => Setting::get('whatsapp_number', '2348083271068'),
            'deliveryInfo' => Setting::get('delivery_info'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1|max:50',
            'variant_id' => 'nullable|exists:product_variants,id',
            'addon_ids' => 'nullable|array',
            'addon_ids.*' => 'integer|exists:product_addons,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $this->cartService->add(
            productId: (int) $validated['product_id'],
            quantity: (int) ($validated['quantity'] ?? 1),
            variantId: !empty($validated['variant_id']) ? (int) $validated['variant_id'] : null,
            addonIds: $validated['addon_ids'] ?? [],
            notes: $validated['notes'] ?? null
        );

        return back()->with('success', 'Item added to your order bag!');
    }

    public function update(Request $request, string $itemKey): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0|max:100',
        ]);

        $this->cartService->update($itemKey, (int) $validated['quantity']);

        return back()->with('success', 'Cart updated successfully.');
    }

    public function destroy(string $itemKey): RedirectResponse
    {
        $this->cartService->remove($itemKey);

        return back()->with('info', 'Item removed from your cart.');
    }

    public function clear(): RedirectResponse
    {
        $this->cartService->clear();

        return redirect()->route('cart.index')->with('info', 'Your cart is now empty.');
    }
}
