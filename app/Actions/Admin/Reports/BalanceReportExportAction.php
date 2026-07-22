<?php

namespace App\Actions\Admin\Reports;

use App\Dtos\Admin\Reports\BalanceReportRequest;
use App\Exports\BalanceReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BalanceReportExportAction
{
    public function __construct(private readonly BalanceReportAction $reportAction)
    {
    }

    public function handle(BalanceReportRequest $request): BinaryFileResponse
    {
        $report = $this->reportAction->handle($request);

        $export = new BalanceReportExport(
            $report['warehouse_name'],
            $request->from_date,
            $request->to_date,
            $report['rows']->all(),
            $report['summary'],
        );

        return Excel::download($export, 'saldo_hisoboti.xlsx');
    }
}
