<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;

class LessProductsExport implements FromQuery, WithHeadingRow, WithMapping
{
    public function __construct()
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
        return Product::query()->whereColumn('quantity', '<=', 'notify_limit');

    }


    public function map($row): array
    {
        return [
            $row->id,
            $row->name, $row->quantity];
    }
}
