<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'customer_id',
        'qty',
        'price',
        'is_resell',
        'commission_rate',
        'resell_commission',
        'resell_profit',
    ];
}