<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_item';

    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'product_name',
        'unit',
        'price',
        'qty',
        'subtotal',
    ];

    protected $casts = [
        'price'    => 'integer',
        'qty'      => 'integer',
        'subtotal' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
