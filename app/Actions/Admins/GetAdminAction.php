<?php

namespace App\Actions\Admins;

use App\Dtos\Admins\FetchAdminDTO;
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
