<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property float $total_price
 */
class OrderItem extends Model
{
    protected $fillable = ['order_id', 'product_id', 'product_price', 'quantity', 'discount', 'total_price', 'selling_price'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
