<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function about(): Response
    {
        return Inertia::render('About', [
            'story' => Setting::get('about_story'),
            'clothingBrand' => Setting::get('clothing_brand_name', 'Biba Clothings'),
            'treatsBrand' => Setting::get('confectionery_brand_name', 'Biba Treats'),
        ]);
    }

    public function contact(): Response
    {
        return Inertia::render('Contact', [
            'email' => Setting::get('contact_email'),
            'phone' => Setting::get('contact_phone'),
            'whatsappNumber' => Setting::get('whatsapp_number'),
            'address' => Setting::get('address'),
            'deliveryInfo' => Setting::get('delivery_info'),
        ]);
    }

    public function privacy(): Response
    {
        return Inertia::render('PrivacyPolicy');
    }

    public function terms(): Response
    {
        return Inertia::render('Terms');
    }
}
