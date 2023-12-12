<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Setting extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['key', 'value'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'key';
}
