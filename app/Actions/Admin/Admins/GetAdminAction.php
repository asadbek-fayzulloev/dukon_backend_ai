<?php

namespace App\Actions\Admin\Admins;

use App\Dtos\Admin\Admins\FetchAdminDTO;
use App\Models\Admin;

class GetAdminAction
{
    public function handle(int $id): array
    {
        $admin = Admin::query()->where('company_id', user()->company_id)->find($id);
        error_if($admin === null, __('admins.not_found'));

        return [
            'admin' => FetchAdminDTO::from($admin)->toArray(),
        ];
    }
}
