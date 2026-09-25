<?php

namespace Step2dev\LazyAdmin\Tests\Fixtures;

class UserWithNameRouteKey extends User
{
    protected $table = 'users';

    public function getRouteKeyName(): string
    {
        return 'name';
    }
}
