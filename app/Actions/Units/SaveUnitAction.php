<?php

namespace App\Actions\Units;

use App\Models\Unit;
use App\Dtos\Units\SaveUnitRequest;

class SaveUnitAction {
    public function handle(SaveUnitRequest $request) : string {
        $unit = new Unit();
        $unit->name = $request->name;
        $unit->save();
        return __('units.store');
    }
}