<?php

namespace App\Actions\Mobile\Units;

use App\Dtos\Mobile\Units\FetchUnitsDTO;
use App\Models\Unit;

class FetchUnitAction
{
    public function handle(): array
    {
        $units = Unit::query()->get();
        return [
            'units' => FetchUnitsDTO::collect($units)
        ];
    }
}