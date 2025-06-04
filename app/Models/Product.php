<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductVariant;


class Product extends Model
{
    protected $fillable = [
        'label',  
        'slug',  
        'category',  
        'sub_category',  
        'meta_title',  
        'meta_description',  
    ];
    protected $casts = [
        'media_gallery' => 'array',
    ];
    public function variants() {
        return $this->hasMany(ProductVariant::class);
    }
}
