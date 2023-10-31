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
        'price',
        'tax',
        'payment_type',
        'recipient_name',
        'recipient_phone',
        'recipient_email',
        'status',
    ];

    // Additional methods and relationships...
}

