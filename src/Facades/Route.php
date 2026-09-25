<?php

namespace Step2dev\LazyAdmin\Facades;

use Closure;

/**
 * Lazy Admin route facade.
 *
 * The admin() macro is registered on Laravel's router at runtime by
 * Step2dev\LazyAdmin\LazyAdminServiceProvider.
 *
 * @method static mixed admin(Closure $callback, array<string, mixed> $attributes = [])
 *
 * @see \Illuminate\Support\Facades\Route
 */
class Route extends \Illuminate\Support\Facades\Route {}
