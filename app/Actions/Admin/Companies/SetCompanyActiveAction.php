<?php

namespace App\Actions\Admin\Companies;

use App\Models\Company;

class SetCompanyActiveAction
{
    public function handle(int $id, bool $isActive): string
    {
        $company = Company::find($id);
        error_if($company === null, __('companies.not_found'));
        $company->is_active = $isActive;
        $company->save();

        return $isActive ? __('companies.activated') : __('companies.deactivated');
    }
}
