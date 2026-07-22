<?php

namespace App\Actions\Admin\Products;

use App\Exports\LessProductsExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LessExportProductAction
{
    public function handle(): BinaryFileResponse
    {
        return Excel::download(new LessProductsExport(user()->company_id), 'kam_qolgan_tovarlar.xlsx');
    }
}