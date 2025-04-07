<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    protected $fillable = [
        'title', 
        'image_path', 
        'image_alt', 
        'description', 
        'link'
    ];

    public function widgets()
    {
        return $this->belongsToMany(Widget::class, 'widget_slide');
    }
    
}
