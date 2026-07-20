<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection): void
    {
        $items = [];
        foreach ($collection as $collection) {
            if ($collection[1] === 'Наименование') {
                continue;
            }
            if ($collection[1] && $collection[5] && $collection[7]) {
                $items[] = [
                    'name' => $collection[1],
                    'quantity' => 0,
                    'net_price' => 0,
                    'price' => 0,
                    'unit_id' => 1
                ];
            }

        }
        Product::query()->insertOrIgnore($items);
    }
}
