<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Widget;

class Page extends Model
{
    protected $fillable = [
        'title',  
        'slug',  
        'created_by',
        'show_in_nav', 
    ];
    public function widgets()
    {
        return $this->hasMany(Widget::class);
    }
}
