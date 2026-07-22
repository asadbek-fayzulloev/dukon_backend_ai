<?php

namespace App\Dtos\Admin\Reports;

use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class ReportDateRangeRequest extends Data
{
    #[Rule(['required', 'date_format:Y-m-d'])]
    public string $from_date;

    #[Rule(['required', 'date_format:Y-m-d', 'after_or_equal:from_date'])]
    public string $to_date;
}
