<?php

namespace App\Actions\Debts;

use App\Dtos\Debts\UpdateDebtRequest;
use App\Models\Debt;

class UpdateDebtAction
{
    public function handle(int $id, UpdateDebtRequest $request): string
    {
        $debt = Debt::query()->find($id);
        error_if($debt === null, __('debts.not_found'));
        $debt->return_date = $request->return_date;
        $debt->amount = $request->amount;
        $debt->save();
        return __('debts.updated');
    }
}