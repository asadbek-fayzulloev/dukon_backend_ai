<?php

namespace App\Actions\Admins;

use App\Models\Admin;

class DestroyAdminAction
{
    public function handle(int $id): string
    {
        $admin = Admin::find($id);
        error_if($admin === null, __('admins.not_found'));
        $admin->delete();

        return __('admins.deleted');
    }
}
