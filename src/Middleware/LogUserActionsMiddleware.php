<?php

namespace Step2dev\LazyAdmin\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogUserActionsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()) {
            Log::info('User Action', [
                'user_id' => $request->user()->id,
                'path' => $request->path(),
                'method' => $request->method(),
                'ip' => $request->ip(),
            ]);
        }
        return $next($request);
    }
}
