<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Header;


class Image extends Model
{
        protected $fillable = [
            'image_path',
            'image_alt',
            'title',
            'credits',
        ];

    public function headers()
    {
        return $this->hasMany(Header::class, 'logo_image_id');
    }
}
