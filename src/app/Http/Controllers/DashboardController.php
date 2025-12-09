<?php

namespace Cms\Http\Controllers;

use App\Http\Controllers\Controller;
use Cms\Services\Dashboard;
use Spark\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return fireline('cms::dashboard');
    }

    public function menu(Request $request, Dashboard $dashboard)
    {
        $slug = $request->getRouteParam(0);
        $menuItem = $dashboard->findMenuItemBySlug($slug);

        if ($menuItem) {
            return fireline('cms::menu.page', compact('menuItem'));
        }

        abort(404);
    }

    public function settings($setting, Dashboard $dashboard)
    {
        if ($dashboard->getSettings()->has($setting)) {
            return fireline('cms::settings.page', [
                'setting' => $dashboard->getSetting($setting),
            ]);
        }

        if (empty($setting)) {
            return redirect('cms.dashboard');
        }

        abort(404);
    }
}