<?php

namespace Step2dev\LazyAdmin\Tests\Fixtures;

class UserWithNameRouteKey extends User
{
    public function getRouteKeyName(): string
    {
        return 'name';
    }
}
