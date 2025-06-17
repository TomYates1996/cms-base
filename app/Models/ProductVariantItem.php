<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantItem extends Model
{
    protected $fillable = [
        'product_variant_id',
        'size_label',
        'sku',
        'price',
        'stock_quantity',
        'quantity_sold',
    ];

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
