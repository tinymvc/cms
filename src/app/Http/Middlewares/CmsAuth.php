<?php

namespace Cms\Http\Middlewares;

use Spark\Contracts\Http\MiddlewareInterface;
use Spark\Http\Request;

class CmsAuth implements MiddlewareInterface
{
    public function handle(Request $request, \Closure $next): mixed
    {
        if (is_guest()) {
            return redirect(auth()->getLoginRoute());
        }

        return $next($request);
    }
}