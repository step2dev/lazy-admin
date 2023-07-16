<?php

namespace Step2dev\LazyAdmin\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LazyAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (auth()->user()?->hasAnyRole(config('lazy.admin.roles')) ?? false) {
            return $next($request);
        }

        return redirect('/');
    }
}
