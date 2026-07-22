<?php

namespace App\Actions\Mobile\Users;

use App\Dtos\PaginationDTO;
use App\Dtos\Mobile\Users\FetchUsersDTO;
use App\Filters\BasesFilter;
use App\Filters\UsersFilter;
use App\Models\Base;
use App\Models\User;
use Illuminate\Http\Request;

class FetchUsersAction
{
    public function handle(Request $request): array
    {
        $query = User::query()->orderByDesc('created_at');
        $query = (new UsersFilter($query))->apply();

        $paginator = $query->paginate(perPage: $request->per_page, page: $request->page);
        $users = array_map(
            fn($user) => FetchUsersDTO::from($user)->toArray(),
            $paginator->items()
        );

        return [
            'users' => $users,
            'paginator' => new PaginationDTO($paginator)
        ];
    }
}