<?php

namespace App\Actions\Debts;

use App\Dtos\Debts\PayDebtRequest;
use App\Enums\DebtStatus;
use App\Enums\OrderStatus;
use App\Models\Debt;
use App\Models\DebtPayment;
use Illuminate\Support\Facades\DB;

class PayDebtAction
{
    public function handle(int $id, PayDebtRequest $request): array
    {
        return DB::transaction(function () use ($id, $request): array {
            $debt = Debt::query()
                ->whereHas('order', fn ($query) => $query->where('shop_id', user()->shop_id))
                ->lockForUpdate()
                ->find($id);

            if ($debt === null) {
                error_response('Qarz topilmadi.', 404);
            }

            if ($request->amount > $debt->remaining_amount) {
                error_response('To‘lov qarz qoldig‘idan oshmasligi kerak.', 422);
            }

            $paidAt = $request->paid_at ?? now();
            $payment = DebtPayment::query()->create([
                'debt_id' => $debt->id,
                'payment_type' => $request->payment_type,
                'amount' => $request->amount,
                'paid_at' => $paidAt,
            ]);

            $debt->remaining_amount -= $request->amount;
            if ($debt->remaining_amount === 0) {
                $debt->status = DebtStatus::PAID->value;
                $debt->paid_at = $paidAt;
            }
            $debt->save();

            $order = $debt->order()->lockForUpdate()->first();
            $order->order_total_paid += $request->amount;
            $order->debt_amount = $debt->remaining_amount;
            if ($debt->remaining_amount === 0) {
                $order->status = OrderStatus::COMPLETED->value;
            }
            $order->save();

            return [
                'payment' => [
                    'id' => $payment->id,
                    'amount' => (int) $payment->amount,
                    'payment_type' => $payment->payment_type,
                    'paid_at' => $payment->paid_at,
                ],
                'debt' => [
                    'id' => $debt->id,
                    'remaining_amount' => (int) $debt->remaining_amount,
                    'status' => $debt->status,
                ],
            ];
        }, 3);
    }
}
