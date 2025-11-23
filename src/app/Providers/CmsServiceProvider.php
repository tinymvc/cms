<?php

namespace Cms\Providers;

use Cms\Services\Dashboard;
use Spark\Container;
use Spark\Facades\Blade;
use Spark\Facades\Route;
use Spark\Http\Auth;

class CmsServiceProvider
{
    public function register(Container $container)
    {
        $container->singleton(Dashboard::class);

        // Customized Auth service registration with configuration
        $container->singleton(Auth::class, fn() => new Auth(config: [
            'session_key' => 'user_id',
            'cache_enabled' => true,
            'cache_name' => 'auth_cache',
            'cache_expire' => '10 minutes',
            'login_route' => 'cms.login',
            'redirect_route' => 'cms.dashboard',
            'cookie_enabled' => true,
            'cookie_name' => 'auth',
            'cookie_expire' => '6 months',
        ]));
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
            'cms.auth' => \Cms\Http\Middlewares\CmsAuth::class,
            'cms.guest' => \Cms\Http\Middlewares\CmsGuest::class,
        ]);

        // Load CMS web routes with the specified route prefix and name
        Route::group(fn() => require "$rootPath/routes/admin.php")
            ->path(env('cms.route_prefix', 'admin'))
            ->name('cms');

        // Load general CMS routes
        require "$rootPath/routes/web.php";

        // Register Blade view path for the CMS
        Blade::setUsePath("$rootPath/resources/views", 'cms');

        /** @var \Cms\Services\Dashboard $dashboard */
        $dashboard = $container->get(Dashboard::class);
        $dashboard->init();

        // Register CLI command for installing the CMS
        $this->registerCliCommands();
    }

    private function registerCliCommands(): void
    {
        if (!is_cli()) {
            return; // Only register commands in CLI environment
        }

        command('cms:install', \Cms\Commands\Install::class, 'Install the TinyMvc CMS package')
            ->help('run this command to install the CMS package, create super user, run migrations and set up initial configuration.');
    }
}