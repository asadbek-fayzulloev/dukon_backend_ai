<?php

namespace App\Http\Controllers\Admin\V1;

use App\Actions\Admin\Statistics\GetStatWidgetAction;
use App\Actions\Admin\Statistics\PaymentStatAction;
use App\Actions\Admin\Statistics\SalesStatAction;
use App\Actions\Admin\Statistics\SellerStatAction;
use App\Dtos\Admin\Statistics\PaymentStatRequest;
use App\Dtos\Admin\Statistics\SalesStatRequest;
use App\Dtos\Admin\Statistics\SellerStatRequest;
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

    public function paymentStat(Request $request, PaymentStatAction $action): array
    {
        return $action->handle(PaymentStatRequest::from($request));
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
