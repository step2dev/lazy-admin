<?php

namespace Step2dev\LazyAdmin\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasRoles;

    protected $guarded = [];

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
