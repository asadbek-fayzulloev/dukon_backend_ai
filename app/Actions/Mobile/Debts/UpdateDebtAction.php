<?php

namespace App\Actions\Mobile\Debts;

use App\Dtos\Mobile\Debts\UpdateDebtRequest;
use App\Models\Debt;

class UpdateDebtAction
{
    public function handle(int $id, UpdateDebtRequest $request): string
    {
        $debt = Debt::query()->find($id);
        error_if($debt === null, __('debts.not_found'));
        $debt->return_date = $request->return_date;
        $debt->save();
        return __('debts.updated');
    }
}
