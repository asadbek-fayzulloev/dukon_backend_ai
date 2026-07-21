<?php

namespace App\Http\Controllers\V1;

use App\Actions\StaticData\FetchStaticDataAction;
use App\Http\Controllers\ApiBaseController;

class StaticDataController extends ApiBaseController
{
    public function index(FetchStaticDataAction $action): array
    {
        return $action->handle();
    }
}
