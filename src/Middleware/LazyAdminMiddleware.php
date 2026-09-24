<?php

namespace Step2dev\LazyAdmin\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LazyAdminMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();

        abort_unless(
            $user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(config('lazy.admin.roles', [])),
            Response::HTTP_FORBIDDEN
        );

        return $next($request);
    }
}
