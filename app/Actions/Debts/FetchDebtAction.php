<?php

namespace App\Actions\Debts;

use App\Dtos\Debts\FetchDebtDTO;
use App\Dtos\PaginationDTO;
use App\Models\Debt;
use Illuminate\Http\Request;

class FetchDebtAction
{
    public function handle(Request $request): array
    {
        $query = Debt::query()
            ->whereHas('user')
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