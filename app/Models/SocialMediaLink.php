<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Footer;


class SocialMediaLink extends Model
{
    protected $table = 'social_media_links'; 
    
    protected $fillable = [
        'link',
        'icon',
        'label',
    ];

    public function footers()
    {
        return $this->belongsToMany(Footer::class, 'footer_social_media_link')
                    ->withTimestamps()
                    ->withPivot('order');
    }
}
