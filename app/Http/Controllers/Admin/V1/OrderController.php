<?php

namespace App\Http\Controllers\Admin\V1;

use App\Actions\Admin\Orders\FetchOrdersAction;
use App\Actions\Admin\Orders\GetOrderAction;
use App\Actions\Admin\Orders\SaveOrderAction;
use App\Actions\Admin\Orders\StatWidgetAction;
use App\Dtos\Admin\Orders\FetchOrderRequest;
use App\Dtos\Admin\Orders\Save\SaveOrderRequest;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;

class OrderController extends ApiBaseController
{
    public function index(Request $request, FetchOrdersAction $action): array
    {
        return $action->handle(FetchOrderRequest::from($request));
    }

    public function statistics(Request $request, StatWidgetAction $action): array
    {
        return $action->handle($request);
    }

    public function store(Request $request, SaveOrderAction $action): array
    {
        return $action->handle(SaveOrderRequest::from($request));
    }

    public function show(int $id, GetOrderAction $action): array
    {
        return $action->handle($id);
    }

}