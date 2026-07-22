<?php

namespace App\Actions\Admin\Invoices;

use App\Dtos\Admin\Invoices\GetInvoiceDTO;
use App\Models\Invoice;

class FetchInvoiceAction
{
    public function handle(int $id): array
    {
        $invoice = Invoice::query()
            ->where('company_id', user()->company_id)
            ->when(user()->shop_id, fn ($query, $shopId) => $query->whereHas('warehouse', fn ($query) => $query->where('shop_id', $shopId)))
            ->with(['warehouse', 'admin', 'histories.product'])
            ->find($id);

        error_if($invoice === null, 'Hujjat topilmadi.', 404);

        return [
            'invoice' => GetInvoiceDTO::from($invoice)->toArray(),
        ];
    }
}
