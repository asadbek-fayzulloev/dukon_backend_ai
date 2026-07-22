<?php

namespace App\Http\Controllers\Admin\V1;

use App\Actions\Admin\Shops\DestroyShopAction;
use App\Actions\Admin\Shops\FetchShopsAction;
use App\Actions\Admin\Shops\SaveShopAction;
use App\Actions\Admin\Shops\UpdateShopAction;
use App\Dtos\Admin\Shops\SaveShopRequest;
use App\Dtos\Admin\Shops\UpdateShopRequest;
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
