<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Slide;
use App\Models\Blog;
use App\Models\Footer;
use App\Models\Category;
use App\Models\Page;
use App\Models\Layout;

class Widget extends Model
{
    protected $fillable = [
        'title', 
        'page_id', 
        'type', 
        'variant', 
        'is_saved',
        'name',
        'template_id',
        'order',
        'subtitle',
        'description',
        'link',
        'link_text',
        'slide_link_text',
        'content',
        'feed_type',
        'to_show',
    ];
    public function pages()
    {
        return $this->belongsToMany(Page::class, 'page_widget')->withPivot('position', 'visibility');
    }
    public function slides()
    {
        return $this->belongsToMany(Slide::class, 'widget_slide');
    }
    public function footers()
    {
        return $this->belongsToMany(Footer::class, 'footer_widget', 'widget_id', 'footer_id');
    }
    public function layouts()
    {
        return $this->belongsToMany(Layout::class, 'layout_widget');
    }
    public function blogs()
    {
        return $this->belongsToMany(Blog::class, 'blog_widget');
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_widget');
    }
}
