<?php

namespace Step2dev\LazyAdmin\Facades;

use Illuminate\Support\Facades\Facade;
use Step2dev\LazyAdmin\Settings\SettingsRegistry;

/**
 * @method static void registerSection(string $id, string $label, string $component, ?string $permission = 'settings.view', int $priority = 100, ?string $description = null)
 *
 * @see SettingsRegistry
 */
class Settings extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SettingsRegistry::class;
    }
}
