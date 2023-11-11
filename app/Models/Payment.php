<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'order_id',
        'reference',
        'payment_type',
        'payment_gateway',
        'payment_gateway_reference',
        // Add more attributes as needed
    ];
}
