<?php

namespace App\Actions\Mobile\Units;

use App\Models\Unit;
use App\Dtos\Mobile\Units\SaveUnitRequest;

class SaveUnitAction {
    public function handle(SaveUnitRequest $request) : string {
        $unit = new Unit();
        $unit->name = $request->name;
        $unit->save();
        return __('units.store');
    }
}