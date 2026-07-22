<?php

namespace App\Actions\Admin\Invoices;

use App\Dtos\Admin\Invoices\FetchInvoiceDTO;
use App\Dtos\PaginationDTO;
use App\Models\Invoice;
use Illuminate\Http\Request;

class FetchInvoicesAction
{
    public function handle(Request $request): array
    {
        $query = Invoice::query()
            ->where('company_id', user()->company_id)
            ->when(user()->shop_id, fn ($query, $shopId) => $query->whereHas('warehouse', fn ($query) => $query->where('shop_id', $shopId)))
            ->withCount('histories')
            ->with(['warehouse', 'admin'])
            ->when($request->type, fn ($query, $type) => $query->where('type', $type))
            ->orderByDesc('created_at');

        $paginator = $query->paginate(perPage: $request->per_page, page: $request->page);

        $invoices = array_map(
            fn ($invoice) => FetchInvoiceDTO::from($invoice)->toArray(),
            $paginator->items()
        );

        return [
            'invoices' => $invoices,
            'paginator' => new PaginationDTO($paginator),
        ];
    }
}
