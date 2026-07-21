<?php

namespace App\Actions\Statistics;

use App\Dtos\Statistics\PaymentStatRequest;
use App\Enums\PaymentType;
use App\Models\OrderPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentStatAction
{
    public function handle(PaymentStatRequest $request): array
    {
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::today()->subDays(30);
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now();

        $totals = OrderPayment::query()
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->select('payment_type', DB::raw('SUM(payed_price) as total'))
            ->groupBy('payment_type')
            ->pluck('total', 'payment_type');

        return [
            'payment_stats' => collect(PaymentType::cases())->map(fn (PaymentType $type): array => [
                'payment_type' => $type->value,
                'label' => $type->label(),
                'amount' => (int) ($totals[$type->value] ?? 0),
            ])->values(),
        ];
    }
}
