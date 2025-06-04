<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\VariantType;
use App\Models\ProductVariant;


class VariantOption extends Model
{
    public function type()
    {
        return $this->belongsTo(VariantType::class);
    }

    public function variants()
    {
        return $this->belongsToMany(ProductVariant::class, 'product_variant_option');
    }
}
