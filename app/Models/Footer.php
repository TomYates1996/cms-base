<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Image;
use App\Models\Page;
use App\Models\SocialMediaLinks;


class Footer extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'section',
        'logo_id',
        'is_saved',
        'name',
        'template_id',
        'order',
    ];

    protected $casts = [
        'is_saved' => 'boolean',
    ];

    public function pages()
    {
        return $this->belongsToMany(Page::class, 'footer_page');
    }

    public function logo()
    {
        return $this->belongsTo(Image::class, 'logo_id');
    }

    public function scopeSaved($query)
    {
        return $query->where('is_saved', true);
    }

    public function duplicateToPage($newPageId)
    {
        return self::create([
            'page_id' => $newPageId,
            'section' => $this->section,
            'logo_id' => $this->logo_id,
            'is_saved' => false, 
            'name' => null,    
        ]);
    }

    public function socialMedia()
    {
        return $this->belongsToMany(SocialMediaLink::class, 'footer_social_media_link')
                    ->withTimestamps()
                    ->withPivot('order');
    }

    public function widgets()
    {
        return $this->belongsToMany(Widget::class, 'footer_widget', 'footer_id', 'widget_id');
    }

}
