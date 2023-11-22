<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_ids',
        'quantities',
        'delivery_info',
        'delivery_fee',
        'price',
        'tax',
        'order_id',
        'payment_type',
        'recipient_name',
        'recipient_phone',
        'recipient_email',
        'status',
        'user_id',
    ];

    // Additional methods and relationships...
}

