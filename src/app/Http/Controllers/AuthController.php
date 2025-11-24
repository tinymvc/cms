<?php

namespace Cms\Http\Controllers;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function login()
    {
        return view('cms::auth.login');
    }
}