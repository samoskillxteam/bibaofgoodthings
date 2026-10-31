<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class WhatsAppCheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected WhatsAppService $whatsAppService
    ) {}

    public function checkout(Request $request)
    {
        $cart = $this->cartService->getValidatedCart();

        if ($cart['is_empty'] || count($cart['items']) === 0) {
            return back()->with('error', 'Your cart is empty. Please add items before checking out.');
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:300',
            'notes' => 'nullable|string|max:500',
        ]);

        $orderData = $this->whatsAppService->generateOrderEnquiry(
            cartData: $cart,
            customerData: $validated,
            ipAddress: $request->ip(),
            userAgent: $request->userAgent()
        );

        // Optionally clear cart after successful checkout generation
        if ($request->boolean('clear_cart', true)) {
            $this->cartService->clear();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'whatsapp_url' => $orderData['whatsapp_url'],
                'reference' => $orderData['reference'],
                'message' => $orderData['message'],
            ]);
        }

        return Inertia::location($orderData['whatsapp_url']);
    }
}
