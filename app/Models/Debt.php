<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property float $amount
 * @property DateTime $return_date
 * @property string $status
 * @property int $order_id
 * @property int $user_id
 */
class Debt extends Model
{
    protected $fillable = ['amount', 'return_date', 'status', 'order_id', 'is_notified', 'user_id'];
    protected $casts = [
        'return_date' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
