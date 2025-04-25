<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Image;
use App\Models\Page;


class Header extends Model
{
    protected $fillable = [
        'logo_image_id',
        'link',
        'page_id',
        'section',
        'is_saved',
        'name',
        'template_id',
        'order',
        'menu_type',
    ];

    public function logo()
    {
        return $this->belongsTo(Image::class, 'logo_image_id');
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

}
