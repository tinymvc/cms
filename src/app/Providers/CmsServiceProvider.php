<?php

namespace Cms\Providers;

use Spark\Container;
use Spark\Facades\Route;

class CmsServiceProvider
{
    public function register(Container $container)
    {
    }

    public function boot(Container $container)
    {
        // Set default configuration for the CMS
        envs(
            array_merge(['cms' => require __DIR__ . '/../../env.php'], env('cms', []))
        );

        /** @var \Spark\Http\Middleware $middleware */
        $middleware = $container->get(\Spark\Http\Middleware::class);

        // Register middlewares
        $middleware->registerMany([
            'cms_auth' => \Cms\Http\Middlewares\CmsAuth::class,
            'cms_guest' => \Cms\Http\Middlewares\CmsGuest::class,
        ]);

        // Load CMS web routes with the specified route prefix and name
        Route::group(fn() => require __DIR__ . '/../routes/admin.php')
            ->path(env('cms.route_prefix', 'admin'))
            ->name('cms');

        // Load general CMS routes
        require __DIR__ . '/../routes/web.php';

        // Register CLI command for installing the CMS
        if (is_cli()) {
            command('cms:install', \Cms\Commands\Install::class, 'Install the TinyMvc CMS package')
                ->help('run this command to install the CMS package, create super user,  run migrations and set up initial configuration.');
        }
    }
}