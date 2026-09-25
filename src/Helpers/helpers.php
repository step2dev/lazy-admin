<?php

use Illuminate\Contracts\View\View;
use Step2dev\LazyAdmin\Localization\Contracts\LocalizationInterface;
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
    function lazyLocalization(): LocalizationInterface
    {
        return app(LocalizationInterface::class);
    }
}

if (! function_exists('lazyView')) {
    /**
     * Render a Lazy Admin package view.
     *
     * Larastan cannot currently resolve package view namespaces such as lazy::.
     *
     * @param  array<string, mixed>  $data
     */
    function lazyView(string $view, array $data = []): View
    {
        /** @var view-string $view */
        return view($view, $data);
    }
}
