<?php

namespace App\Actions\Orders;

use App\Dtos\Orders\Save\SaveOrderRequest;
use App\Dtos\Orders\SaveOrderItemRequest;
use App\Dtos\Orders\SaveOrderPaymentRequest;
use App\Enums\OrderStatus;
use App\Models\Debt;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\User;
use App\Models\WarehouseProduct;
use Exception;
use Illuminate\Support\Facades\DB;

class SaveOrderAction
{
    public function handle(SaveOrderRequest $request): array
    {
        DB::beginTransaction();
        try {
            $order = new Order();
            $order->seller_id = user()->id;

            // Prepare items
            $items = $request->items->toCollection()->map(
                function (SaveOrderItemRequest $itemData) {
                    $orderItem = new OrderItem();
                    $orderItem->fill($itemData->toArray());
                    $orderItem->total_price = $itemData->quantity * $itemData->product_price;
                    return $orderItem;
                }
            );

            // ✅ Check product stock availability
            $productIds = $items->pluck('product_id')->unique();
            $products = WarehouseProduct::query()->whereIn('id', $productIds)->get()->keyBy('id');

            foreach ($items as $item) {
                $product = $products->get($item->product_id);
                if (!$product) {
                    throw new Exception("Product with ID {$item->product_id} not found.");
                }

                if ($product->quantity < $item->quantity) {
                    throw new Exception("Not enough stock for product '{$product->name}'. Requested: {$item->quantity}, Available: {$product->quantity}");
                }
            }

            // Prepare payments
            $payments = $request->payments->toCollection()->map(
                function (SaveOrderPaymentRequest $paymentData) {
                    $payment = new OrderPayment();
                    $payment->fill($paymentData->toArray());
                    return $payment;
                }
            );

            // Set order info
            $order->order_total_price = $items->sum('total_price');
            $order->order_total_paid = $payments->sum('payed_price');
            $order->shop_id = user()->shop_id;
            $order->save();

            // If not fully paid, associate user and create debt
            if ($order->order_total_price > $order->order_total_paid) {
                $user = User::query()->find($request->user_id);
                if ($user === null) {
                    $user = new User();
                    $user->phone = $request->user->phone;
                    $user->name = $request->user->name;
                    $user->save();
                }
                $order->user_id = $user->id;
                $order->save();

                $debt = new Debt();
                $debt->amount = $order->order_total_price - $order->order_total_paid;
                $debt->return_date = $request->debt_return_date;
                $debt->user_id = $user->id;
                $debt->order_id = $order->id;
                $debt->status = OrderStatus::CREATED->value;
                $debt->is_notified = false;
                $debt->save();
            }

            // Save related items and payments
            $order->items()->saveMany($items);
            $order->payments()->saveMany($payments);

            // Optional: Reduce stock quantities
            foreach ($items as $item) {
                $product = $products->get($item->product_id);
                $product->decrement('quantity', $item->quantity);
            }

            DB::commit();

            return [
                'order' => [
                    'id' => $order->id
                ]
            ];
        } catch (Exception $exception) {
            DB::rollBack();
            return error_response($exception->getMessage() . ' - ' . $exception->getLine(), 500);
        }
    }
}
