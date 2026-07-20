<?php

namespace App\Actions\Units;
use App\Dtos\Units\UpdateUnitRequest;
use App\Models\Unit;

class UpdateUnitAction 
{
    public function handle(int $id, UpdateUnitRequest $request){
        $unit = Unit::find($id);
        error_if($unit === null, __('units.not_found'));
        $unit->name = $request->name;
        $unit->save();
        return __('units.updated');
    }
}