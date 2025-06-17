<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;


class Order extends Model
{
    protected $fillable = [
        'order_number',
        'price',
        'completed',
        'status',
        'paypal_order_id',
        'paypal_capture_id',
        'paypal_response',
        'first_name',
        'last_name',
        'email',
        'phone',
        'delivery_date',
        'delivery_method',
        'delivery_address',
        'city',
        'postcode',
        'country',
        'notes',
        'ip_address',
    ];
    public function products()
    {
        return $this->belongsToMany(ProductVariantItem::class, 'order_items')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }
}
