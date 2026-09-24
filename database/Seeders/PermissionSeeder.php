<?php

namespace Step2dev\LazyAdmin\Database\Seeders;

use Illuminate\Database\Seeder;
use Step2dev\LazyAdmin\Authorization\AuthorizationManager;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(AuthorizationManager::class)->seedDefaults();
    }
}
