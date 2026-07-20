<?php

namespace App\Http\Controllers;

use App\Http\Interceptors\HasInterceptors;

class ApiBaseController extends Controller
{
    use HasInterceptors;
}
