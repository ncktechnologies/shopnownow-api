<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tax',
        'delivery_option',
        'discount_option',
        'discount_type',
        'discount_value',
        'thumbnail',
        'hidden',
        'band_id', // Add this line
    ];
}
