<?php

namespace App\Actions\Admin\Units;
use App\Models\Unit;

class DestroyUnitAction 
{
    public function handle(int $id) : string
    {
        $unit = Unit::find($id);
        error_if($unit === null, __('units.not_found'));
        $unit->delete();
        return __('units.deleted');
    }
}