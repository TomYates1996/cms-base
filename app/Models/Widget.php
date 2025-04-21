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
        'is_saved',
        'name',
        'template_id',
        'order',
    ];
    public function slides()
    {
        return $this->belongsToMany(Slide::class, 'widget_slide');
    }
}
