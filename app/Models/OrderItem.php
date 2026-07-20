<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property float $total_price
 */
class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'warehouse_product_id', 'product_price',
        'net_price', 'quantity', 'discount', 'total_price', 'selling_price',
    ];

    protected $casts = ['quantity' => 'decimal:3'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function warehouseProduct(): BelongsTo
    {
        return $this->belongsTo(WarehouseProduct::class);
    }
}
