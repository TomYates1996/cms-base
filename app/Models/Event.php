<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $casts = [
        'amenities' => 'array',
        'social_links' => 'array',
        'opening_hours' => 'array',
        'media_gallery' => 'array',
        'pricing' => 'array',
        'tags' => 'array',
        'accessibility_info' => 'array',
    ];
}
