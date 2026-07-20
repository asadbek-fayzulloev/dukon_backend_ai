<?php

namespace App\Observers;

use App\Enums\DebtStatus;
use App\Models\Debt;
use App\Models\Order;

class OrderObserver
{
    public function creating(Order $order): void
    {
        $order->seller_id = auth()->user()->id;
        $order->shop_id = auth()->user()->shop_id;
    }

    public function created(Order $order): void
    {
        if ($order->order_total_price > $order->order_total_paid && $order->user_id) {
            $debt = new Debt();
            $debt->amount = ($order->order_total_price - $order->order_total_paid);
            $debt->remaining_amount = $debt->amount;
            $debt->status = DebtStatus::OPEN->value;
            $debt->order_id = $order->id;
            $debt->user_id = $order->user_id;
            $debt->return_date = $order->debt_return_date;
            $debt->is_notified = 0;
            $debt->save();
        }

    }
}
