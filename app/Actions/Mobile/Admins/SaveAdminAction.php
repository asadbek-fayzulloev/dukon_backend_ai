<?php

namespace App\Actions\Mobile\Admins;

use App\Dtos\Mobile\Admins\SaveAdminRequest;
use App\Models\Admin;
use Spatie\Permission\Models\Role;

class SaveAdminAction
{
    public function handle(SaveAdminRequest $request): string
    {
        $admin = new Admin();
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = $request->password; // hashed automatically via the model's cast
        $admin->shop_id = $request->shop_id;
        $admin->save();

        // Pass a Role instance (not a raw id) so Spatie skips its default-guard-scoped
        // lookup and only checks that 'api' is one of Admin's possible guards, which it is.
        $role = $request->role_id ? Role::find($request->role_id) : null;
        $admin->syncRoles($role ? [$role] : []);

        return __('admins.stored');
    }
}
