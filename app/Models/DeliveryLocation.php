<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryLocation extends Model
{
    use HasFactory;
    
    protected $fillable = ['band_id', 'location', 'price', 'hidden'];
}
