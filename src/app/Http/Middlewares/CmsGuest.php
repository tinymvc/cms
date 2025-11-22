<?php

namespace Cms\Http\Middlewares;

use Spark\Contracts\Http\MiddlewareInterface;
use Spark\Http\Request;

class CmsGuest implements MiddlewareInterface
{
    public function handle(Request $request, \Closure $next): mixed
    {
        if (is_logged()) {
            return redirect(auth()->getRedirectRoute());
        }

        return $next($request);
    }
}