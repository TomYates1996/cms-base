<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Slide;

class Widget extends Model
{
    protected $fillable = [
        'title', 
        'page_id', 
        'type', 
    ];
    public function slides()
    {
        return $this->belongsToMany(Slide::class, 'widget_slide');
    }
}
