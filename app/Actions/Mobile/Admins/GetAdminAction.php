<?php

namespace App\Actions\Mobile\Admins;

use App\Dtos\Mobile\Admins\FetchAdminDTO;
use App\Models\Admin;

class GetAdminAction
{
    public function handle(int $id): array
    {
        $admin = Admin::query()->find($id);
        error_if($admin === null, __('admins.not_found'));

        return [
            'admin' => FetchAdminDTO::from($admin)->toArray(),
        ];
    }
}
