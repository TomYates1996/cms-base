<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Widget;
use App\Models\SubCategory;


class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    public function widgets()
    {
        return $this->belongsToMany(Widget::class, 'category_widget');
    }
    public function subcategories()
    {
        return $this->hasMany(SubCategory::class, 'parent_cat_id');
    }
}
