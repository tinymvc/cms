<?php

namespace Cms\Providers;

use Cms\Services\Dashboard;
use Spark\Container;
use Spark\Facades\Blade;
use Spark\Facades\Route;

class CmsServiceProvider
{
    public function register(Container $container)
    {
        $container->singleton(Dashboard::class, function () {
            $menu = config('cms.dashboard.menu', []);
            $config = config('cms.dashboard.config', []);

            return new Dashboard($menu, $config);
        });
    }

    public function boot(Container $container)
    {
        // Determine the root path of the CMS package
        $rootPath = dirname(__DIR__, 2);

        // Set default configuration for the CMS
        envs(
            array_merge(['cms' => require "$rootPath/env.php"], env('cms', []))
        );

        /** @var \Spark\Http\Middleware $middleware */
        $middleware = $container->get(\Spark\Http\Middleware::class);

        // Register middlewares
        $middleware->registerMany([
            'cms_auth' => \Cms\Http\Middlewares\CmsAuth::class,
            'cms_guest' => \Cms\Http\Middlewares\CmsGuest::class,
        ]);

        // Load CMS web routes with the specified route prefix and name
        Route::group(fn() => require "$rootPath/routes/admin.php")
            ->path(env('cms.route_prefix', 'admin'))
            ->name('cms');

        // Load general CMS routes
        require "$rootPath/routes/web.php";

        // Register Blade view path for the CMS
        Blade::setUsePath("$rootPath/resources/views", 'cms');

        // Register CLI command for installing the CMS
        if (is_cli()) {
            command('cms:install', \Cms\Commands\Install::class, 'Install the TinyMvc CMS package')
                ->help('run this command to install the CMS package, create super user, run migrations and set up initial configuration.');
        }
    }
}