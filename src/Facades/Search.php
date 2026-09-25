<?php

namespace Step2dev\LazyAdmin\Facades;

use Closure;
use Illuminate\Support\Facades\Facade;
use Step2dev\LazyAdmin\Search\SearchRegistry;

/**
 * @method static void register(string $id, Closure $provider, ?string $permission = null, int $priority = 100)
 *
 * @see SearchRegistry
 */
class Search extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SearchRegistry::class;
    }
}
