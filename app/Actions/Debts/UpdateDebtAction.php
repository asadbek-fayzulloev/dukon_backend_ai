<?php

namespace App\Actions\Debts;

use App\Dtos\Debts\UpdateDebtRequest;
use App\Models\Debt;

class UpdateDebtAction
{
    public function handle(int $id, UpdateDebtRequest $request): string
    {
        $debt = Debt::query()
            ->whereHas('order', fn ($query) => $query->where('shop_id', user()->shop_id))
            ->find($id);
        error_if($debt === null, __('debts.not_found'));
        $debt->return_date = $request->return_date;
        $debt->save();
        return __('debts.updated');
    }
}
