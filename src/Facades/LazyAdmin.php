<?php

namespace Step2dev\LazyAdmin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Step2dev\LazyAdmin\LazyAdmin
 */
class LazyAdmin extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Step2dev\LazyAdmin\LazyAdmin::class;
    }
}
