<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $fillable = [
        'code',
        'discount_percentage',
        'expires_at',
        'active',
    ];

    protected $casts = [
        'expires_at' => 'date',
        'active' => 'boolean',
    ];

    public function scopeValid($query)
    {
        return $query->where('active', true)
                     ->where(function ($q) {
                         $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                     });
    }
}
