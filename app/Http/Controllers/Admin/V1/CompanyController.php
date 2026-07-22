<?php

namespace App\Http\Controllers\Admin\V1;

use App\Actions\Admin\Companies\DestroyCompanyAction;
use App\Actions\Admin\Companies\FetchCompaniesAction;
use App\Actions\Admin\Companies\SaveCompanyAction;
use App\Actions\Admin\Companies\UpdateCompanyAction;
use App\Dtos\Admin\Companies\SaveCompanyRequest;
use App\Dtos\Admin\Companies\UpdateCompanyRequest;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;

class CompanyController extends ApiBaseController
{
    public function index(FetchCompaniesAction $action): array
    {
        return $action->handle();
    }

    public function store(Request $request, SaveCompanyAction $action): string
    {
        return $action->handle(SaveCompanyRequest::from($request));
    }

    public function update(int $id, Request $request, UpdateCompanyAction $action): string
    {
        return $action->handle($id, UpdateCompanyRequest::from($request));
    }

    public function destroy(int $id, DestroyCompanyAction $action): string
    {
        return $action->handle($id);
    }
}
