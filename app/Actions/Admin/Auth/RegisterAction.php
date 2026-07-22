<?php

namespace App\Actions\Admin\Auth;

use App\Dtos\Admin\Auth\RegisterRequest;
use App\Models\Admin;
use App\Models\Company;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;

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
                'name' => $request->company_name,
                'company_id' => $company->id,
            ]);

            $admin = new Admin();
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->password = $request->password;
            $admin->company_id = $company->id;
            $admin->shop_id = $shop->id;
            $admin->save();
        });

        return __('auth.registered');
    }
}
