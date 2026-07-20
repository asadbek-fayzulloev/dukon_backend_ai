<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): array
    {
        $user = Admin::query()
            ->where('username', $request->username)
            ->first();

        error_if($user == null, 'Login yoki parol noto‘g‘ri');
        error_unless(Hash::check($request->password, $user->password), 'Login yoki parol noto‘g‘ri');

        $token = $user->createToken('shop')->plainTextToken;
        return [
            'token' => $token
        ];
    }
}
