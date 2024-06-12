<?php

namespace Step2dev\LazyAdmin\Facades;

use Illuminate\Support\Facades\Facade;
use Step2dev\LazyAdmin\Navigation\Breadcrumb\BreadcrumbManager;

class Breadcrumb extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BreadcrumbManager::class;
    }
}
