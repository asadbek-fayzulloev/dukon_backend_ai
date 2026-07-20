<?php

namespace App\Actions\Auth;

use App\Dtos\Auth\MeDto;

class MeAction
{
    public function handle(): array
    {
        return [
            'user' => MeDto::from(user())->toArray()
        ];
    }
}