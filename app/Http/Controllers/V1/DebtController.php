<?php

namespace App\Http\Controllers\V1;

use App\Actions\Debts\FetchDebtAction;
use App\Actions\Debts\GetDebtAction;
use App\Actions\Debts\UpdateDebtAction;
use App\Dtos\Debts\UpdateDebtRequest;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;

class DebtController extends ApiBaseController
{
    public function index(Request $request, FetchDebtAction $action): array
    {
        return $action->handle($request);
    }

    public function update(int $id, Request $request, UpdateDebtAction $action): string
    {
        return $action->handle($id, UpdateDebtRequest::from($request));
    }

    public function show(int $id, GetDebtAction $action): array
    {
        return $action->handle($id);
    }
}