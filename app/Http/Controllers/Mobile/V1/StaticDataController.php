<?php

namespace App\Http\Controllers\Mobile\V1;

use App\Actions\Mobile\StaticData\FetchStaticDataAction;
use App\Http\Controllers\ApiBaseController;

class StaticDataController extends ApiBaseController
{
    public function index(FetchStaticDataAction $action): array
    {
        return $action->handle();
    }
}
