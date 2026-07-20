<?php

namespace App\Actions\Debts;

use App\Dtos\Debts\GetDebtDTO;
use App\Models\Debt;

class GetDebtAction
{
    public function handle(int $id): array
    {
        $debt = Debt::query()->find($id);
        error_if($debt === null, __('debts.not_found'));
        return [
            'debt' => GetDebtDTO::from($debt)->toArray()
        ];
    }
}