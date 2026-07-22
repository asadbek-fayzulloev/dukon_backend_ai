<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;

class LessProductsExport implements FromQuery, WithHeadingRow, WithMapping
{
    public function __construct(private readonly ?int $companyId = null)
    {
    }

    public function headings(): array
    {
        return [
            '№',
            'Nomi',
            'Soni',
        ];
    }

    public function query()
    {
        // products.quantity moved to warehouse_products (per-warehouse batches),
        // so "low stock" compares the aggregated stock across warehouses.
        return Product::query()
            ->when($this->companyId, fn ($query, $companyId) => $query->where('company_id', $companyId))
            ->selectSub(
                DB::table('warehouse_products')
                    ->selectRaw('COALESCE(SUM(quantity), 0)')
                    ->whereColumn('warehouse_products.product_id', 'products.id'),
                'total_quantity'
            )
            ->whereRaw(
                '(SELECT COALESCE(SUM(quantity), 0) FROM warehouse_products WHERE warehouse_products.product_id = products.id) <= notify_limit'
            );
    }


    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->total_quantity,
        ];
    }
}
