<?php

namespace Step2dev\LazyAdmin\Localization;

class LocalizationManager implements Contracts\LocalizationInterface
{
    private function isMultiLanguage(): bool
    {
        return config('lazy/localization.multi_language');
    }

    public function setLocale(string|null $locale = null): string|null
    {
        if ($this->isMultiLanguage()) {
            if (class_exists('\Arcanedev\Localization\Localization')) {
                return localization()->setLocale($locale);
            }

            if (class_exists('\Mcamara\LaravelLocalization\LaravelLocalization')) {
                return \Mcamara\LaravelLocalization\Facades\LaravelLocalization::setLocale($locale);
            }
        }

        return null;
    }

    public function getLocale(): string
    {
        if (class_exists('\Arcanedev\Localization\Localization')) {
            return localization()->getCurrentLocale();
        }

        if (class_exists('\Mcamara\LaravelLocalization\LaravelLocalization')) {
            return \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
        }

        return app()->getLocale();
    }

    public function getSupportedLocales(): array
    {
        if (class_exists('\Arcanedev\Localization\Localization')) {
            return localization()->getSupportedLocales();
        }

        if (class_exists('\Mcamara\LaravelLocalization\LaravelLocalization')) {
            return \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales();
        }

        return [];
    }

    public function setRouteLocale(string $prefix = '', string|null $locale = null): string
    {
        $locale = $this->setLocale($locale);

        if ($locale) {
            return $locale.'/'.$prefix;
        }

        return $prefix;
    }
}
