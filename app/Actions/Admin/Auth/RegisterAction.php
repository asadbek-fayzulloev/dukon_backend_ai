<?php

namespace App\Actions\Admin\Auth;

use App\Dtos\Admin\Auth\RegisterRequest;
use App\Models\Admin;
use App\Models\Company;
use App\Models\Shop;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RegisterAction
{
    public function handle(RegisterRequest $request): string
    {
        DB::transaction(function () use ($request) {
            $company = Company::create([
                'name' => $request->company_name,
                'is_active' => false,
            ]);

            $shop = Shop::create([
                'name' => "{$request->company_name} do'koni",
                'company_id' => $company->id,
            ]);

            Warehouse::create([
                'name' => "{$request->company_name} ombori",
                'shop_id' => $shop->id,
                'company_id' => $company->id,
            ]);

            $admin = new Admin();
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->password = $request->password;
            $admin->company_id = $company->id;
            $admin->shop_id = $shop->id;
            $admin->save();

            // Role names are unique across the whole guard (no multi-tenant "teams"
            // support enabled), so the company id keeps every company's superadmin
            // role from colliding with another's.
            $role = Role::create(['name' => "superadmin-{$company->id}", 'guard_name' => 'api']);
            $role->syncPermissions(Permission::all());
            $admin->syncRoles([$role]);
        });

        return __('auth.registered');
    }
}
