<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = ['title', 'slug', 'exerpt', 'content', 'author_id', 'image_id', 'image_path', 'status', 'is_featured', 'category_id', 'published_at', 'meta_title', 'meta_description'];

    public function widgets()
    {
        return $this->belongsToMany(Widget::class, 'blog_widget')
                    ->withPivot('position')
                    ->orderBy('blog_widget.position');
    }
    public function image()
    {
        return $this->belongsTo(Image::class);
    }
}
