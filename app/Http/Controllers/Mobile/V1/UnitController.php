<?php

namespace App\Http\Controllers\Mobile\V1;

use App\Actions\Mobile\Units\FetchUnitAction;
use Illuminate\Http\Request;
use App\Actions\Mobile\Units\SaveUnitAction;
use App\Dtos\Mobile\Units\SaveUnitRequest;
use App\Actions\Mobile\Units\UpdateUnitAction;
use App\Dtos\Mobile\Units\UpdateUnitRequest;
use App\Actions\Mobile\Units\DestroyUnitAction;
use App\Http\Controllers\ApiBaseController;

class UnitController extends ApiBaseController
{
    public function index(FetchUnitAction $action): array
    {
        return $action->handle();
    }
    public function store(Request $request, SaveUnitAction $action): string
    {
        return $action->handle(SaveUnitRequest::from($request));
    }
    public function update(int $id, Request $request, UpdateUnitAction $action): string {
        return $action->handle($id, UpdateUnitRequest::from($request));
    }
    public function destroy(int $id, DestroyUnitAction $action):string{
        return $action->handle($id);
    }
}