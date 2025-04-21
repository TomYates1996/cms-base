<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Image;
use App\Models\Page;


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

    public function page()
    {
        return $this->belongsTo(Page::class);
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
}
