<?php

namespace App\Actions\Products;

use App\Exports\LessProductsExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LessExportProductAction
{
    public function handle(): BinaryFileResponse
    {
        return Excel::download(new LessProductsExport(), 'kam_qolgan_tovarlar.xlsx');
    }
}