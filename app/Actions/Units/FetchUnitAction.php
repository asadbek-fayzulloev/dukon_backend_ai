<?php

namespace App\Actions\Units;

use App\Dtos\Units\FetchUnitsDTO;
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