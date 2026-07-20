<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = str_replace(['/', 'api.v1.'], ['.', ''], $request->route()->getName());
        $authRequired = in_array('auth:api', $request->route()->gatherMiddleware());
        error_if($authRequired && Auth::check() && !Auth::user()->can($routeName),
            'You don`t have permission to do this action ' . $routeName, 403);
        return $next($request);
    }
}
