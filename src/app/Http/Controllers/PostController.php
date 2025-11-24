<?php

namespace Cms\Http\Controllers;

use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public function index()
    {
        return view('cms::posts.index');
    }
}