<?php

use Step2dev\LazyAdmin\Localization\Contracts\LocalizationInterface;
use Step2dev\LazyAdmin\Services\SettingService;

if (! function_exists('settings')) {
    /**
     * Get the available container instance.
     *
     * @return mixed|SettingService
     */
    function setting(?string $key = null, mixed $default = null): mixed
    {
        /** @var SettingService $settings */
        $settings = app('settings');
        if ($key === null) {
            return $settings;
        }

        return $settings->get($key, $default);
    }
}

if (! function_exists('lazyLocalization')) {
    /**
     * Get the available container instance.
     */
    function lazyLocalization(): mixed
    {
        return app(LocalizationInterface::class);
    }
}
