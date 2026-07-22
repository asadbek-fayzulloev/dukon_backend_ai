<?php

namespace App\Actions\Mobile\Auth;

use App\Dtos\Mobile\Auth\MeDto;

class MeAction
{
    public function handle(): array
    {
        return [
            'user' => MeDto::from(user())->toArray()
        ];
    }
}