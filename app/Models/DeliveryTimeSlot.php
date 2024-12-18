<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryTimeSlot extends Model
{
    use HasFactory;
    protected $fillable = ['start_time', 'end_time', 'is_available','band_id',];
}
