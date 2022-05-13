<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'site_id',
        'title',
        'image',
        'live_link',
        'category',
        'main_description',
        'publish_date'
    ];
}
