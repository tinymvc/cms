<?php

namespace Cms\Http\Controllers;

use App\Http\Controllers\Controller;

class TaxonomyController extends Controller
{
    public function index()
    {
        return view('cms::taxonomies.index');
    }
}