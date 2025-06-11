<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductVariant;


class Product extends Model
{
    protected $fillable = [
        'label',  
        'slug',  
        'category_id',  
        'sub_category_id',  
        'meta_title',  
        'meta_description',  
    ];
    protected $casts = [
        'media_gallery' => 'array',
    ];
    public function variants() {
        return $this->hasMany(ProductVariant::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }
}
