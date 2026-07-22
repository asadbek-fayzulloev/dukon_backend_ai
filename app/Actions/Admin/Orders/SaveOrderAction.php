<?php

namespace App\Actions\Admin\Orders;

use App\Dtos\Admin\Orders\Save\SaveOrderRequest;
use App\Enums\DebtStatus;
use App\Enums\OrderStatus;
use App\Models\Debt;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaveOrderAction
{
    public function handle(SaveOrderRequest $request): array
    {
        return DB::transaction(function () use ($request): array {
            $seller = user();
            $uuid = $request->uuid ?? (string) Str::uuid();

            $existingOrder = Order::query()
                ->where('uuid', $uuid)
                ->where('shop_id', $seller->shop_id)
                ->first();

            if ($existingOrder !== null) {
                return $this->response($existingOrder, true);
            }

            $warehouse = Warehouse::query()->find($request->warehouse_id);
            $this->assert(
                $warehouse !== null && (int) $warehouse->shop_id === (int) $seller->shop_id,
                'Ombor ushbu do‘konga tegishli emas.'
            );

            $requestedItems = $request->items->toCollection()
                ->groupBy('product_id')
                ->map(fn (Collection $items): float => (float) $items->sum('quantity'));

            $batches = WarehouseProduct::query()
                ->where('warehouse_id', $warehouse->id)
                ->whereIn('product_id', $requestedItems->keys())
                ->where('quantity', '>', 0)
                ->orderBy('created_at')
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->groupBy('product_id');

            $preparedItems = collect();

            foreach ($requestedItems as $productId => $requestedQuantity) {
                $productBatches = $batches->get($productId, collect());
                $availableQuantity = (float) $productBatches->sum('quantity');

                $this->assert(
                    $availableQuantity + 0.000001 >= $requestedQuantity,
                    "Mahsulot #{$productId} yetarli emas. So‘raldi: {$requestedQuantity}, mavjud: {$availableQuantity}."
                );

                $sellingPrice = (int) $productBatches->max('price');
                $remaining = $requestedQuantity;

                foreach ($productBatches as $batch) {
                    if ($remaining <= 0.000001) {
                        break;
                    }

                    $consumed = min($remaining, (float) $batch->quantity);
                    $preparedItems->push([
                        'product_id' => (int) $productId,
                        'warehouse_product_id' => $batch->id,
                        'product_price' => $sellingPrice,
                        'net_price' => (int) $batch->net_price,
                        'quantity' => $consumed,
                        'discount' => 0,
                        'total_price' => (int) round($sellingPrice * $consumed),
                    ]);

                    $batch->quantity = (float) $batch->quantity - $consumed;
                    $batch->save();
                    $remaining -= $consumed;
                }
            }

            $subtotal = (int) $preparedItems->sum('total_price');
            $discountAmount = $this->discountAmount(
                $subtotal,
                $request->discount_type,
                $request->discount_value
            );
            $total = $subtotal - $discountAmount;
            $payments = $request->payments->toCollection();
            $this->assert(
                $payments->pluck('payment_type')->unique()->count() === $payments->count(),
                'Bir xil to‘lov turi bir buyurtmada takrorlanmasligi kerak.'
            );
            $paid = (int) $payments->sum('payed_price');

            $this->assert($paid <= $total, 'To‘lov summasi savdo summasidan oshmasligi kerak.');

            $debtAmount = $total - $paid;
            $customer = $this->resolveCustomer($request, $debtAmount > 0);

            $order = new Order();
            $order->fill([
                'uuid' => $uuid,
                'user_id' => $customer?->id,
                'seller_id' => $seller->id,
                'shop_id' => $seller->shop_id,
                'company_id' => $seller->company_id,
                'warehouse_id' => $warehouse->id,
                'device_id' => $request->device_id,
                'subtotal' => $subtotal,
                'discount_type' => $discountAmount > 0 ? $request->discount_type : null,
                'discount_value' => $discountAmount > 0 ? $request->discount_value : null,
                'discount_amount' => $discountAmount,
                'discount' => $discountAmount,
                'order_total_price' => $total,
                'order_total_paid' => $paid,
                'debt_amount' => $debtAmount,
                'status' => $debtAmount > 0 ? OrderStatus::DEBT->value : OrderStatus::COMPLETED->value,
                'sold_at' => $request->sold_at ?? now(),
                'synced_at' => now(),
            ]);
            $order->save();

            $order->items()->createMany($preparedItems->all());
            $order->payments()->createMany($payments->map(fn ($payment): array => [
                'payment_type' => $payment->payment_type,
                'payed_price' => $payment->payed_price,
            ])->all());

            if ($debtAmount > 0) {
                Debt::query()->create([
                    'amount' => $debtAmount,
                    'remaining_amount' => $debtAmount,
                    'return_date' => $request->debt_return_date,
                    'user_id' => $customer->id,
                    'order_id' => $order->id,
                    'status' => DebtStatus::OPEN->value,
                    'is_notified' => false,
                    'company_id' => $seller->company_id,
                ]);
            }

            return $this->response($order->fresh(), false);
        }, 3);
    }

    private function discountAmount(int $subtotal, ?string $type, ?float $value): int
    {
        if ($type === null && ($value === null || $value == 0)) {
            return 0;
        }

        $this->assert($type !== null && $value !== null, 'Chegirma turi va qiymati birga yuborilishi kerak.');

        if ($type === 'percentage') {
            $this->assert($value <= 100, 'Foizli chegirma 100% dan oshmasligi kerak.');

            return (int) round($subtotal * $value / 100);
        }

        $amount = (int) round($value);
        $this->assert($amount <= $subtotal, 'Chegirma summasi savdo summasidan oshmasligi kerak.');

        return $amount;
    }

    private function resolveCustomer(SaveOrderRequest $request, bool $required): ?User
    {
        $customer = $request->user_id !== null
            ? User::query()->find($request->user_id)
            : null;

        if ($customer === null && $request->user !== null) {
            $customer = User::query()->firstOrCreate(
                ['phone' => $request->user->phone],
                ['name' => $request->user->name]
            );
        }

        $this->assert(!$required || $customer !== null, 'Qarzli savdo uchun mijoz majburiy.');
        $this->assert(!$required || $request->debt_return_date !== null, 'Qarz muddati majburiy.');

        return $customer;
    }

    private function response(Order $order, bool $duplicate): array
    {
        return [
            'order' => [
                'id' => $order->id,
                'uuid' => $order->uuid,
                'subtotal' => (int) $order->subtotal,
                'discount_amount' => (int) $order->discount_amount,
                'total' => (int) $order->order_total_price,
                'paid' => (int) $order->order_total_paid,
                'debt' => (int) $order->debt_amount,
                'status' => $order->status,
            ],
            'duplicate' => $duplicate,
        ];
    }

    private function assert(bool $condition, string $message): void
    {
        if (!$condition) {
            error_response($message, 422);
        }
    }
}
