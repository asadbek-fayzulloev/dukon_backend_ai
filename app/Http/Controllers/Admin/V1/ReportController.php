<?php

namespace App\Http\Controllers\Admin\V1;

use App\Actions\Admin\Reports\BalanceReportAction;
use App\Actions\Admin\Reports\ProfitLossReportAction;
use App\Actions\Admin\Reports\SalesReportAction;
use App\Actions\Admin\Reports\StockReportAction;
use App\Dtos\Admin\Reports\BalanceReportRequest;
use App\Dtos\Admin\Reports\ReportDateRangeRequest;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;

class ReportController extends ApiBaseController
{
    public function sales(Request $request, SalesReportAction $action): array
    {
        return $action->handle(ReportDateRangeRequest::from($request));
    }

    public function profitLoss(Request $request, ProfitLossReportAction $action): array
    {
        return $action->handle(ReportDateRangeRequest::from($request));
    }

    public function stock(StockReportAction $action): array
    {
        return $action->handle();
    }

    public function balance(Request $request, BalanceReportAction $action): array
    {
        return $action->handle(BalanceReportRequest::from($request));
    }
}
