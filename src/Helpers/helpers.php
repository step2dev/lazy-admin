<?php

use Step2dev\LazyAdmin\Localization\Contracts\LocalizationInterface;
use Step2dev\LazyAdmin\Localization\LocalizationManager;
use Step2Dev\LazySetting\LazySetting;

if (! function_exists('setting')) {
    /**
     * Get the settings service or a setting value.
     */
    function setting(?string $key = null, mixed $default = null): mixed
    {
        /** @var LazySetting $settings */
        $settings = app('setting');

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
    function lazyLocalization(): LocalizationManager
    {
        return app(LocalizationInterface::class);
    }
}
