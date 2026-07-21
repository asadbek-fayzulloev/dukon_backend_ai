<?php

namespace App\Http\Controllers\V1;

use App\Actions\Statistics\GetStatWidgetAction;
use App\Actions\Statistics\SalesStatAction;
use App\Actions\Statistics\SellerStatAction;
use App\Dtos\Statistics\SalesStatRequest;
use App\Dtos\Statistics\SellerStatRequest;
use App\Http\Controllers\ApiBaseController;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class StatisticsController extends ApiBaseController
{
    public function widgets(Request $request, GetStatWidgetAction $action): array
    {
        return $action->handle($request);
    }

    public function sellerStat(Request $request, SellerStatAction $action): array
    {
        return $action->handle(SellerStatRequest::from($request));
    }

    public function salesStat(Request $request, SalesStatAction $action): array
    {
        return $action->handle(SalesStatRequest::from($request));
    }
    public function other(){
        return [
            'widgets' => [
                'warehouses' => Warehouse::query()->withCount('products')->get()->pluck('name', 'productsCount')->toArray(),
                'shops' => [],

            ]
        ];
    }
}
