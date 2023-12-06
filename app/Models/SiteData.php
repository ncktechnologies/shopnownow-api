<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteData extends Model
{
    protected $fillable = ['faq', 'terms_and_conditions', 'privacy_policy', 'contact_data'];
}
