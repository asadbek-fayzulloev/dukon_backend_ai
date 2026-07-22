<?php

namespace App\Actions\Admin\Companies;

use App\Models\Company;

class DestroyCompanyAction
{
    public function handle(int $id): string
    {
        $company = Company::find($id);
        error_if($company === null, __('companies.not_found'));
        $company->delete();

        return __('companies.deleted');
    }
}
