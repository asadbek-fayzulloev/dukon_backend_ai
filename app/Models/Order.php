<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $user_id
 * @property float $order_total_paid
 * @property float $order_total_price
 * @property int $id
 * @property int $seller_id
 * @property int $shop_id
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid', 'user_id', 'seller_id', 'shop_id', 'warehouse_id', 'device_id',
        'subtotal', 'discount_type', 'discount_value', 'discount_amount', 'discount',
        'order_total_price', 'order_total_paid', 'debt_amount', 'status', 'sold_at', 'synced_at',
        'company_id',
    ];

    protected $casts = [
        'discount_value' => 'decimal:3',
        'sold_at' => 'datetime',
        'synced_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'seller_id');
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function debt(): HasOne
    {
        return $this->hasOne(Debt::class, 'order_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

}
