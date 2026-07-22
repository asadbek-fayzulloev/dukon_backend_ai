<?php

namespace App\Http\Controllers\Admin\V1;

use App\Actions\Admin\Invoices\FetchInvoiceAction;
use App\Actions\Admin\Invoices\FetchInvoicesAction;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;

class InvoiceController extends ApiBaseController
{
    public function index(Request $request, FetchInvoicesAction $action): array
    {
        return $action->handle($request);
    }

    public function show(int $id, FetchInvoiceAction $action): array
    {
        return $action->handle($id);
    }
}
