<?php

namespace App\Actions\Admin\Companies;

use App\Dtos\Admin\Companies\SaveCompanyRequest;
use App\Models\Company;

class SaveCompanyAction
{
    public function handle(SaveCompanyRequest $request): string
    {
        $company = new Company();
        $company->name = $request->name;
        $company->save();

        return __('companies.stored');
    }
}
