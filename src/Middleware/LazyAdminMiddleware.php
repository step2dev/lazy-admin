<?php

namespace Step2dev\LazyAdmin\Middleware;

use Closure;
use Illuminate\Http\Request;

class LazyAdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (auth()->user()?->hasAnyRole(config('lazy.admin.roles')) ?? false) {
            return $next($request);
        }

        return redirect('/');
    }
}
