<?php

namespace App\Models;
use App\Models\ProductVariantItem;
use App\Models\Product;



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

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function items() {
        return $this->hasMany(ProductVariantItem::class);
    }
}
