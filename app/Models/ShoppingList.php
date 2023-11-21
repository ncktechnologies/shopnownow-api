<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingList extends Model
{
    use HasFactory;

    // Add fillable products and quantities to the model
    protected $fillable = ['product_ids', 'quantities', 'user_id'];

    // Cast the product_ids and quantities to arrays
    protected $casts = [
        'product_ids' => 'array',
        'quantities' => 'array',
    ];
}
