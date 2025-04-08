<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Widget;
use App\Models\Image;


class Slide extends Model
{
    protected $fillable = [
        'title', 
        'description', 
        'link',
        'image_id',
    ];

    public function widgets()
    {
        return $this->belongsToMany(Widget::class, 'widget_slide');
    }
    public function image()
    {
        return $this->belongsTo(Image::class);
    }
}
