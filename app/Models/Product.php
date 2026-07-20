<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $name
 * @property float $net_price
 * @property float $price
 * @property int $unit_id
 * @property float|null $notify_limit
 * @property float $quantity
 */
class Product extends Model
{
    protected $fillable = ['name', 'image', 'unit_id', 'notify_limit','category_id','code'];
    protected $casts = [
        'quantity' => 'float'
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
