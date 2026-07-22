<?php

namespace App\Http\Controllers\V1;

use App\Actions\Shops\DestroyShopAction;
use App\Actions\Shops\FetchShopsAction;
use App\Actions\Shops\SaveShopAction;
use App\Actions\Shops\UpdateShopAction;
use App\Dtos\Shops\SaveShopRequest;
use App\Dtos\Shops\UpdateShopRequest;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;

class ShopController extends ApiBaseController
{
    public function index(FetchShopsAction $action): array
    {
        return $action->handle();
    }

    public function store(Request $request, SaveShopAction $action): string
    {
        return $action->handle(SaveShopRequest::from($request));
    }

    public function update(int $id, Request $request, UpdateShopAction $action): string
    {
        return $action->handle($id, UpdateShopRequest::from($request));
    }

    public function destroy(int $id, DestroyShopAction $action): string
    {
        return $action->handle($id);
    }
}
