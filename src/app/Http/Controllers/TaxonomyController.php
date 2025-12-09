<?php

namespace Cms\Http\Controllers;

use App\Http\Controllers\Controller;

class TaxonomyController extends Controller
{
    public function index()
    {
        return fireline('cms::taxonomies.index');
    }
}