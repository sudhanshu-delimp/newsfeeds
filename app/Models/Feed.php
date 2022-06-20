<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Site;

class Feed extends Model
{
    use HasFactory;
    protected $fillable = [
        'site_id',
        'url'
    ];

    public function site(){
        return $this->belongsTo('App\Models\Site','site_id');
    }
}
