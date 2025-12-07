<?php

namespace Cms\Http\Controllers;

use App\Http\Controllers\Controller;
use Cms\Services\Dashboard;
use Spark\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('cms::dashboard');
    }

    public function menu(Request $request, Dashboard $dashboard)
    {
        $slug = $request->getRouteParam(0);
        $menuItem = $dashboard->findMenuItemBySlug($slug);

        if ($menuItem) {
            $dashboard->setCurrentMenuItem($menuItem);

            return view('cms::menu.page', compact('menuItem'));
        }

        abort(404);
    }
}