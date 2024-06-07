<?php

namespace Step2dev\LazyAdmin\Localization;

class LocalizationManager implements Contracts\LocalizationInterface
{
    private bool $isMultiLanguage = false;

    private bool $isArcanedevLocalization = false;

    private bool $isMcamaraLocalization = false;

    public function __construct()
    {
        $this->isMultiLanguage = config('lazy.localization.multi_language');
        $this->isArcanedevLocalization = class_exists('\Arcanedev\Localization\Localization');
        $this->isMcamaraLocalization = class_exists('\Mcamara\LaravelLocalization\LaravelLocalization');
    }

    public function setLocale(?string $locale = null): ?string
    {
        if (! $this->isMultiLanguage) {
            return null;
        }

        if ($this->isArcanedevLocalization) {
            return localization()->setLocale($locale);
        }

        if ($this->isMcamaraLocalization) {
            return \Mcamara\LaravelLocalization\Facades\LaravelLocalization::setLocale($locale);
        }

        trigger_error('No localization package found', E_USER_WARNING);
        return null;
    }

    public function getLocale(): string
    {
        if ($this->isArcanedevLocalization) {
            return localization()->getCurrentLocale();
        }

        if ($this->isMcamaraLocalization) {
            return \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
        }

        return app()->getLocale();
    }

    public function getSupportedLocales(): array
    {
        if ($this->isArcanedevLocalization) {
            return localization()->getSupportedLocales();
        }

        if ($this->isMcamaraLocalization) {
            return \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales();
        }

        return [];
    }

    public function setRouteLocale(string $prefix = '', ?string $locale = null): string
    {
        $locale = $this->setLocale($locale);

        if ($locale) {
            return $locale.'/'.$prefix;
        }

        return $prefix;
    }
}
