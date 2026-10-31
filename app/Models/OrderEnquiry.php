<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderEnquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'status',
        'customer_name',
        'customer_phone',
        'customer_address',
        'customer_notes',
        'items',
        'subtotal',
        'delivery_fee',
        'total',
        'whatsapp_message',
        'whatsapp_url',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'items' => 'array',
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total' => 'decimal:2',
    ];
}
