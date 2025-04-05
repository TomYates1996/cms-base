<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title',  
        'slug',  
        'created_by',
        'show_in_nav', 
    ];
}
