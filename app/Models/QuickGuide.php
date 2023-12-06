<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class QuickGuide extends Model
{
    protected $fillable = ['title', 'body', 'image_path', 'is_hidden'];

    public function getImageUrlAttribute()
    {
        return Storage::url($this->image_path);
    }
}
