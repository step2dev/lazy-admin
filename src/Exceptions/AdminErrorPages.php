<?php

namespace Step2dev\LazyAdmin\Exceptions;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

final class AdminErrorPages
{
    public function __invoke(Throwable $exception, Request $request): ?\Illuminate\Http\Response
    {
        if ($request->expectsJson() || $request->is('livewire/*') || ! $this->isAdminRequest($request)) {
            return null;
        }

        if (! $exception instanceof HttpExceptionInterface) {
            return null;
        }

        $status = $exception->getStatusCode();

        if (! in_array($status, [401, 403, 404, 419, 429, 500, 503], true)) {
            return null;
        }

        return response()->view('lazy::errors.page', [
            'status' => $status,
            'homeUrl' => url('/'.trim((string) config('lazy.admin.route.prefix', 'admin'), '/')),
        ], $status, $exception->getHeaders());
    }

    private function isAdminRequest(Request $request): bool
    {
        $domain = config('lazy.admin.route.domain');

        if (is_string($domain) && $domain !== '' && $request->getHost() !== $domain) {
            return false;
        }

        $routeName = $request->route()?->getName();
        $namePrefix = trim((string) config('lazy.admin.route.name', 'admin.'), '.');

        if (is_string($routeName) && $namePrefix !== '' &&
            ($routeName === $namePrefix || str_starts_with($routeName, $namePrefix.'.'))) {
            return true;
        }

        $prefix = trim((string) config('lazy.admin.route.prefix', 'admin'), '/');

        if ($prefix === '') {
            return false;
        }

        $segments = explode('/', trim($request->decodedPath(), '/'));
        $prefixSegments = explode('/', $prefix);

        if (array_slice($segments, 0, count($prefixSegments)) === $prefixSegments) {
            return true;
        }

        // Permit a locale before the admin prefix on a missing route.
        return count($segments) > count($prefixSegments)
            && preg_match('/^[a-z]{2}(?:-[A-Za-z]{2})?$/', $segments[0]) === 1
            && array_slice($segments, 1, count($prefixSegments)) === $prefixSegments;
    }
}
