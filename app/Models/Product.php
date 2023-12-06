<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'thumbnail_url',
        'price',
        'unit_of_measurement',
        'availability',
        'category_id',
    ];

    public function band()
    {
        return $this->belongsTo(Band::class);
    }
}
