<?php

namespace App\Actions\Admin\Debts;

use App\Dtos\Admin\Debts\UpdateDebtRequest;
use App\Models\Debt;

class UpdateDebtAction
{
    public function handle(int $id, UpdateDebtRequest $request): string
    {
        $debt = Debt::query()->where('company_id', user()->company_id)->find($id);
        error_if($debt === null, __('debts.not_found'));
        $debt->return_date = $request->return_date;
        $debt->save();
        return __('debts.updated');
    }
}
