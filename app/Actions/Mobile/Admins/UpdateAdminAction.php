<?php

namespace App\Actions\Mobile\Admins;

use App\Dtos\Mobile\Admins\UpdateAdminRequest;
use App\Models\Admin;
use Spatie\Permission\Models\Role;

class UpdateAdminAction
{
    public function handle(int $id, UpdateAdminRequest $request): string
    {
        $admin = Admin::find($id);
        error_if($admin === null, __('admins.not_found'));

        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->shop_id = $request->shop_id;

        if (! empty($request->password)) {
            $admin->password = $request->password; // hashed automatically via the model's cast
        }

        $admin->save();

        $role = $request->role_id ? Role::find($request->role_id) : null;
        $admin->syncRoles($role ? [$role] : []);

        return __('admins.updated');
    }
}
