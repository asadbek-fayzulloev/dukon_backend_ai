<?php

namespace App\Actions\Admin\Admins;

use App\Dtos\Admin\Admins\FetchAdminDTO;
use App\Dtos\PaginationDTO;
use App\Filters\AdminsFilter;
use App\Models\Admin;
use Illuminate\Http\Request;

class FetchAdminsAction
{
    public function handle(Request $request): array
    {
        $query = Admin::query()->where('company_id', user()->company_id)->orderByDesc('created_at');
        $query = (new AdminsFilter($query))->apply();

        $paginator = $query->paginate(perPage: $request->per_page, page: $request->page);
        $admins = array_map(
            fn ($admin) => FetchAdminDTO::from($admin)->toArray(),
            $paginator->items()
        );

        return [
            'admins' => $admins,
            'paginator' => new PaginationDTO($paginator),
        ];
    }
}
