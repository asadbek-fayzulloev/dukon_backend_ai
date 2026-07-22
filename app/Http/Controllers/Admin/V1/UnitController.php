<?php

namespace App\Http\Controllers\Admin\V1;

use App\Actions\Admin\Units\FetchUnitAction;
use Illuminate\Http\Request;
use App\Actions\Admin\Units\SaveUnitAction;
use App\Dtos\Admin\Units\SaveUnitRequest;
use App\Actions\Admin\Units\UpdateUnitAction;
use App\Dtos\Admin\Units\UpdateUnitRequest;
use App\Actions\Admin\Units\DestroyUnitAction;
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