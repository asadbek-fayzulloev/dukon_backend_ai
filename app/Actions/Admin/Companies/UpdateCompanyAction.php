<?php

namespace App\Actions\Admin\Companies;

use App\Dtos\Admin\Companies\UpdateCompanyRequest;
use App\Models\Company;

class UpdateCompanyAction
{
    public function handle(int $id, UpdateCompanyRequest $request): string
    {
        $company = Company::find($id);
        error_if($company === null, __('companies.not_found'));
        $company->name = $request->name;
        $company->save();

        return __('companies.updated');
    }
}
