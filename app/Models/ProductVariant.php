<?php

namespace App\Models;
use App\Models\ProductVariantItem;



use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'title', 
        'name', 
        'slug',  
        'category',  
        'sub_category',  
        'description',
        'short_description',
        'sku',
        'price',
        'stock_quantity',
        'media_gallery',
        'thumbnail_image',
    ];
    protected $casts = [
        'media_gallery' => 'array',
    ];

    public function items() {
        return $this->hasMany(ProductVariantItem::class);
    }
}
