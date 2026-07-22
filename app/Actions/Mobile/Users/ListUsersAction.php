<?php

namespace App\Actions\Mobile\Users;

use App\Dtos\Mobile\Users\FetchUsersDTO;
use App\Filters\UsersFilter;
use App\Models\User;
use Illuminate\Http\Request;

class ListUsersAction
{
    public function handle(Request $request): array
    {
        $query = User::query()
            ->orderByDesc('created_at')
            ->limit(10);
        $query = (new UsersFilter($query))->apply();
        $users = $query->get();
        return [
            'users' => FetchUsersDTO::collect($users)->toArray()
        ];
    }
}