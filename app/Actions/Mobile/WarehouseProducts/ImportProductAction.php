<?php

namespace App\Actions\Mobile\WarehouseProducts;

use App\Dtos\Mobile\WarehouseProducts\Import\ImportProductRequest;
use Illuminate\Support\Facades\DB;

class ImportProductAction
{
    public function handle(ImportProductRequest $request): string
    {
        $warehouseId = DB::table('warehouses')->orderBy('id')->value('id');

        if (!$warehouseId) {
            throw new \Exception('No warehouse found');
        }

        DB::transaction(function () use ($request, $warehouseId) {
            $ids = collect($request->products)->pluck('id')->toArray();

            // Mavjud yozuvlarni product_id + net_price + price bo'yicha xaritalaymiz
            $existingRows = DB::table('warehouse_products')
                ->whereIn('product_id', $ids)
                ->where('warehouse_id', $warehouseId)
                ->get(['id', 'product_id', 'net_price', 'price']);

            $existingMap = [];
            foreach ($existingRows as $row) {
                $key = $this->makeKey($row->product_id, $row->net_price, $row->price);
                $existingMap[$key] = $row->id;
            }

            $toInsert = [];
            $toUpdate = []; // [warehouse_products.id => qo'shiladigan miqdor]

            foreach ($request->products as $product) {
                $key = $this->makeKey($product->id, $product->net_price, $product->price);

                if (isset($existingMap[$key])) {
                    $rowId = $existingMap[$key];
                    $toUpdate[$rowId] = ($toUpdate[$rowId] ?? 0) + $product->quantity;
                } else {
                    $toInsert[] = $product;
                }
            }

            // INSERT — narx/net_price bo'yicha mos keladigan yozuv topilmasa, yangi qator
            if (!empty($toInsert)) {
                $insertData = array_map(fn($p) => [
                    'product_id'   => $p->id,
                    'warehouse_id' => $warehouseId,
                    'quantity'     => $p->quantity,
                    'net_price'    => $p->net_price,
                    'price'        => $p->price,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ], $toInsert);

                DB::table('warehouse_products')->insert($insertData);
            }

            // UPDATE — narx/net_price mos kelsa, faqat quantity oshiriladi
            if (!empty($toUpdate)) {
                $cases    = '';
                $bindings = [];

                foreach ($toUpdate as $rowId => $qty) {
                    $cases .= "WHEN id = ? THEN quantity + ? ";
                    $bindings[] = $rowId;
                    $bindings[] = $qty;
                }

                $updateIds    = array_keys($toUpdate);
                $placeholders = implode(',', array_fill(0, count($updateIds), '?'));
                $bindings     = array_merge($bindings, $updateIds);

                DB::update("
                    UPDATE warehouse_products
                    SET quantity   = CASE $cases END,
                        updated_at = NOW()
                    WHERE id IN ($placeholders)
                ", $bindings);
            }

            // Har bir kirim qatori uchun tarixiy yozuv — warehouse_products faqat
            // joriy yig'indini saqlaydi, "kirim tarixi" shu jadval orqali ko'rsatiladi.
            $movementRows = array_map(fn($p) => [
                'product_id'   => $p->id,
                'warehouse_id' => $warehouseId,
                'admin_id'     => optional(user())->id,
                'quantity'     => $p->quantity,
                'net_price'    => $p->net_price,
                'price'        => $p->price,
                'created_at'   => now(),
                'updated_at'   => now(),
            ], $request->products);

            DB::table('warehouse_product_imports')->insert($movementRows);
        });

        return __('products.imported');
    }

    /**
     * product_id + net_price + price bo'yicha unikal kalit yasaydi,
     * float taqqoslashdagi xatolarni oldini olish uchun 2 xonali formatda.
     */
    private function makeKey(int $productId, float $netPrice, float $price): string
    {
        return $productId . '|' . number_format($netPrice, 2, '.', '') . '|' . number_format($price, 2, '.', '');
    }
}
