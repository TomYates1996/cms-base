<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Widget;
use App\Models\Header;
use App\Models\Footer;

class Page extends Model
{
    protected $fillable = [
        'title',  
        'slug',  
        'created_by',
        'show_in_nav', 
        'section',
        'level',
        'parent_id',
        'is_link',
        'linked_page_id',
    ];
    public function widgets()
    {
        return $this->hasMany(Widget::class);
    }
    public function headers()
    {
        return $this->belongsToMany(Header::class);
    }
    public function footers()
    {
        return $this->belongsToMany(Footer::class, 'footer_page');
    }
    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id');
    }
}
