<?php

use App\Http\Interceptors\Response\DetectKeyed;
use App\Http\Interceptors\Response\ErrorResponseCheck;
use App\Http\Interceptors\Response\ExtractAnonymousCollection;
use App\Http\Interceptors\Response\MakePageable;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;

return [
    'enabled' => env('INTERCEPTOR_ENABLED', true),
    'silent' => env('INTERCEPTOR_SILENT', false), // If true, will not throw exceptions
    'interceptors' => [
        ErrorResponseCheck::class,
        'api' => fn() => request()->is('api/*'),
    ],
    'ignored_types' => [
        Response::class,
        View::class,
    ],
    // Interceptor groups
    'groups' => [
        'api' => [
            DetectKeyed::class,
            ExtractAnonymousCollection::class,
            MakePageable::class,
        ],
        
    ],
];
