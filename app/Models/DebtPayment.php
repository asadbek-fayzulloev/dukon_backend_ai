<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebtPayment extends Model
{
    protected $fillable = ['debt_id', 'payment_type', 'amount', 'paid_at'];

    protected $casts = ['paid_at' => 'datetime'];

    public function debt(): BelongsTo
    {
        return $this->belongsTo(Debt::class);
    }
}
