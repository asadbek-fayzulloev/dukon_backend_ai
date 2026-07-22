<?php

namespace App\Actions\Admin\Units;

use App\Dtos\Admin\Units\FetchUnitsDTO;
use App\Models\Unit;

class FetchUnitAction
{
    public function handle(): array
    {
        $units = Unit::query()->where('company_id', user()->company_id)->get();
        return [
            'units' => FetchUnitsDTO::collect($units)
        ];
    }
}