<?php

namespace Step2dev\LazyAdmin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Step2dev\LazyAdmin\Authorization\AuthorizationManager;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $tables = (array) config('permission.table_names', []);

        if (
            empty($tables['roles'])
            || empty($tables['permissions'])
            || ! Schema::hasTable($tables['roles'])
            || ! Schema::hasTable($tables['permissions'])
        ) {
            return;
        }

        app(AuthorizationManager::class)->seedDefaults();
    }
}
