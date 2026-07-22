<?php

namespace App\Actions\Admin\Admins;

use App\Models\Admin;

class DestroyAdminAction
{
    public function handle(int $id): string
    {
        $admin = Admin::where('company_id', user()->company_id)->find($id);
        error_if($admin === null, __('admins.not_found'));
        $admin->delete();

        return __('admins.deleted');
    }
}
