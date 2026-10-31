<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderEnquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderEnquiryController extends Controller
{
    public function index(Request $request): Response
    {
        $query = OrderEnquiry::latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $enquiries = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/Orders/Index', [
            'enquiries' => $enquiries,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function show(OrderEnquiry $enquiry): Response
    {
        return Inertia::render('Admin/Orders/Show', [
            'enquiry' => $enquiry,
        ]);
    }

    public function updateStatus(Request $request, OrderEnquiry $enquiry): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,contacted,completed,cancelled',
        ]);

        $enquiry->update(['status' => $validated['status']]);

        return back()->with('success', "Order enquiry status updated to {$validated['status']}.");
    }
}
