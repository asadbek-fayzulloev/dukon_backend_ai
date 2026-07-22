<?php

namespace App\Http\Controllers\Mobile\V1;

use App\Actions\Mobile\Debts\FetchDebtAction;
use App\Actions\Mobile\Debts\GetDebtAction;
use App\Actions\Mobile\Debts\PayDebtAction;
use App\Actions\Mobile\Debts\UpdateDebtAction;
use App\Dtos\Mobile\Debts\PayDebtRequest;
use App\Dtos\Mobile\Debts\UpdateDebtRequest;
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

    public function pay(int $id, Request $request, PayDebtAction $action): array
    {
        return $action->handle($id, PayDebtRequest::from($request));
    }
}
