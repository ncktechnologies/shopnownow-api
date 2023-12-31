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
        'delivery_time_slot',
        'coupon_code',
        'scheduled_date',
    ];

    // Additional methods and relationships...
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

