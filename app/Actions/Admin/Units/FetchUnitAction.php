<?php

namespace App\Actions\Admin\Units;

use App\Dtos\Admin\Units\FetchUnitsDTO;
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