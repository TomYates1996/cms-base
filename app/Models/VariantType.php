<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\VariantOption;

class VariantType extends Model
{
    public function options()
    {
        return $this->hasMany(VariantOption::class);
    }
}
