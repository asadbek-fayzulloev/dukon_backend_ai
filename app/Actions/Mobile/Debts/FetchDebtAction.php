<?php

namespace App\Actions\Mobile\Debts;

use App\Dtos\Mobile\Debts\FetchDebtDTO;
use App\Dtos\PaginationDTO;
use App\Models\Debt;
use Illuminate\Http\Request;

class FetchDebtAction
{
    public function handle(Request $request): array
    {
        $query = Debt::query()
            ->whereHas('user')
            ->whereHas('order', fn ($query) => $query->where('shop_id', user()->shop_id))
            ->with(['user', 'order'])
            ->orderByDesc('created_at');
        $paginator = $query->paginate(perPage: $request->per_page, page: $request->page);
        $debts = array_map(
            fn($debt) => FetchDebtDTO::from($debt)->toArray(),
            $paginator->items()
        );

        return [
            'debts' => $debts,
            'paginator' => new PaginationDTO($paginator)
        ];

    }
}
