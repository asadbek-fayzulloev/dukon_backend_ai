<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property float $amount
 * @property DateTime $return_date
 * @property string $status
 * @property int $order_id
 * @property int $user_id
 */
class Debt extends Model
{
    use HasFactory;
    protected $fillable = [
        'amount', 'remaining_amount', 'return_date', 'paid_at', 'status',
        'order_id', 'is_notified', 'user_id', 'company_id',
    ];
    protected $casts = [
        'return_date' => 'datetime',
        'paid_at' => 'datetime',
        'is_notified' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(DebtPayment::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
