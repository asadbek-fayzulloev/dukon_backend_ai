<?php

namespace App\Actions\Admin\Invoices;

use App\Dtos\Admin\Invoices\GetInvoiceDTO;
use App\Models\Invoice;

class FetchInvoiceAction
{
    public function handle(int $id): array
    {
        $invoice = Invoice::query()
            ->whereHas('warehouse', fn ($query) => $query->where('shop_id', user()->shop_id))
            ->with(['warehouse', 'admin', 'histories.product'])
            ->find($id);

        error_if($invoice === null, 'Hujjat topilmadi.', 404);

        return [
            'invoice' => GetInvoiceDTO::from($invoice)->toArray(),
        ];
    }
}
