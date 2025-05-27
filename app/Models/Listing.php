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
        'prices' => 'array',  
        'tags' => 'array',
        'accessibility_info' => 'array',
    ];
    public function events()
    {
        return $this->hasMany(Event::class, 'organiser_id')
        ->select('id', 'title', 'slug', 'thumbnail_image', 'short_description', 'start_datetime', 'end_datetime' ,'organiser_id');
    }

    public function relatedListings()
    {
        return $this->belongsToMany(Listing::class, 'listing_related', 'listing_id', 'related_listing_id');
    }

    public function inverselyRelatedListings()
    {
        return $this->belongsToMany(Listing::class, 'listing_related', 'related_listing_id', 'listing_id');
    }
}
