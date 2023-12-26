<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Band extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'minimum', 'bulk_discount_percentage', 'bulk_discount_amount', 'general_discount', 'discount_enabled', 'free_delivery_threshold'];
}
