<?php

namespace App\Actions\Admin\Units;
use App\Dtos\Admin\Units\UpdateUnitRequest;
use App\Models\Unit;

class UpdateUnitAction 
{
    public function handle(int $id, UpdateUnitRequest $request){
        $unit = Unit::where('company_id', user()->company_id)->find($id);
        error_if($unit === null, __('units.not_found'));
        $unit->name = $request->name;
        $unit->save();
        return __('units.updated');
    }
}