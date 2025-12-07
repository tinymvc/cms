<?php

namespace Cms\Http\Controllers;

use App\Http\Controllers\Controller;
use Cms\Modules\CustomPostType;
use Cms\Services\Dashboard;
use Spark\Http\Request;

class PostController extends Controller
{
    private CustomPostType $postType;

    public function __construct(Request $request, Dashboard $dashboard)
    {
        $slug = str($request->getPath())
            ->remove(dashboard_prefix())
            ->trim('/');

        $postType = $dashboard->getPostType($slug->explode('/')->first());
        if (!$postType) {
            abort(404); // Post type not found
        }

        $this->postType = $postType;

        $menuItem = $dashboard->findMenuItemBySlug($slug);

        if ($menuItem) {
            $dashboard->setCurrentMenuItem($menuItem);
        }
    }

    public function index()
    {
        return view('cms::posts.index');
    }
}