<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    protected $fillable = [
        'title', 
        'slug', 
        'description',
        'short_description',
        'category',
        'sub_category',
        'tags',
        'address',
        'city',
        'region',
        'country',
        'postcode',
        'latitude',
        'longitude',
        'phone_number',
        'email',
        'website',
        'media_gallery',
        'thumbnail_image',
        'opening_hours',
        'prices' ,
        'booking_url',
        'reservation_email',
        'featured',
        'owner_id',
        'published_at',
        'social_links',
        'amenities',
        'accessibility_info',
    ];
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
