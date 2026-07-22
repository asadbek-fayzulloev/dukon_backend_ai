<?php

namespace App\Actions\Admins;

use App\Dtos\Admins\SaveAdminRequest;
use App\Models\Admin;

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

        return __('admins.stored');
    }
}
