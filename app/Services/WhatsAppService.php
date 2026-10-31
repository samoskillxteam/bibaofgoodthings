<?php

namespace App\Services;

use App\Models\OrderEnquiry;
use App\Models\Setting;
use Illuminate\Support\Str;

class WhatsAppService
{
    public function generateOrderEnquiry(array $cartData, array $customerData = [], ?string $ipAddress = null, ?string $userAgent = null): array
    {
        $reference = 'BIB-' . date('Ymd') . '-' . strtoupper(Str::random(5));
        $businessName = Setting::get('business_name', 'Biba of Good Things');
        $rawPhone = Setting::get('whatsapp_number', '2348083271068');
        $phone = $this->normalizePhoneNumber($rawPhone);

        $customerName = trim($customerData['name'] ?? '');
        $customerPhone = trim($customerData['phone'] ?? '');
        $customerAddress = trim($customerData['address'] ?? '');
        $customerNotes = trim($customerData['notes'] ?? '');

        $message = "🛍️ *NEW ORDER ENQUIRY*\n";
        $message .= "*{$businessName}*\n";
        $message .= "Order Ref: *#{$reference}*\n\n";

        if ($customerName || $customerPhone || $customerAddress) {
            $message .= "👤 *Customer Details:*\n";
            if ($customerName) $message .= "• Name: {$customerName}\n";
            if ($customerPhone) $message .= "• Phone: {$customerPhone}\n";
            if ($customerAddress) $message .= "• Delivery Address: {$customerAddress}\n";
            $message .= "\n";
        }

        $message .= "📦 *Order Items:*\n";

        $i = 1;
        foreach ($cartData['items'] as $item) {
            $brand = $item['brand_name'] ?? 'Biba';
            $message .= "{$i}. *{$item['product_name']}* ({$brand})\n";

            if (!empty($item['variant_title'])) {
                $message .= "   • Option: {$item['variant_title']}\n";
            }

            if (!empty($item['addons'])) {
                $addonNames = array_column($item['addons'], 'name');
                $message .= "   • Add-ons: " . implode(', ', $addonNames) . "\n";
            }

            if (!empty($item['notes'])) {
                $message .= "   • Note: {$item['notes']}\n";
            }

            $message .= "   • Qty: {$item['quantity']} × {$item['formatted_unit_price']}\n";
            $message .= "   • Subtotal: {$item['formatted_line_total']}\n\n";
            $i++;
        }

        $message .= "--------------------------------\n";
        $message .= "💰 *Estimated Order Total:* {$cartData['formatted_subtotal']}\n\n";

        if ($customerNotes) {
            $message .= "📝 *Order Notes:* {$customerNotes}\n\n";
        }

        $deliveryInfo = Setting::get('delivery_info');
        if ($deliveryInfo) {
            $message .= "ℹ️ {$deliveryInfo}\n\n";
        }

        $message .= "Please confirm item availability, final delivery or collection arrangements.\nThank you!";

        $whatsappUrl = "https://wa.me/{$phone}?text=" . rawurlencode($message);

        // Store enquiry record for business owner to track
        $enquiry = OrderEnquiry::create([
            'reference' => $reference,
            'status' => 'pending',
            'customer_name' => $customerName ?: null,
            'customer_phone' => $customerPhone ?: null,
            'customer_address' => $customerAddress ?: null,
            'customer_notes' => $customerNotes ?: null,
            'items' => $cartData['items'],
            'subtotal' => $cartData['subtotal'],
            'delivery_fee' => 0.00,
            'total' => $cartData['subtotal'],
            'whatsapp_message' => $message,
            'whatsapp_url' => $whatsappUrl,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);

        return [
            'reference' => $reference,
            'enquiry_id' => $enquiry->id,
            'whatsapp_url' => $whatsappUrl,
            'message' => $message,
            'phone' => $phone,
        ];
    }

    public function normalizePhoneNumber(string $phone): string
    {
        $clean = preg_replace('/[^\d]/', '', $phone);

        // Handle Nigerian local format e.g. 08012345678 -> 2348012345678
        if (str_starts_with($clean, '0') && strlen($clean) === 11) {
            $clean = '234' . substr($clean, 1);
        }

        if (empty($clean)) {
            return '2348083271068';
        }

        return $clean;
    }
}
