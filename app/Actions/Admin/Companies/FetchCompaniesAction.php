<?php

namespace App\Actions\Admin\Companies;

use App\Dtos\Admin\Companies\FetchCompanyDTO;
use App\Models\Company;

class FetchCompaniesAction
{
    public function handle(): array
    {
        $companies = Company::query()->orderByDesc('created_at')->get();

        return [
            'companies' => FetchCompanyDTO::collect($companies),
        ];
    }
}
