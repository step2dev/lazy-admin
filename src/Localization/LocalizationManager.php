<?php

namespace Step2dev\LazyAdmin\Localization;

class LocalizationManager implements Contracts\LocalizationInterface
{
    private bool $isMultiLanguage;

    private bool $isArcanedevLocalization;

    private bool $isMcamaraLocalization;

    public function __construct()
    {
        $this->isMultiLanguage = config('lazy.localization.multi_language', false);
        $this->isArcanedevLocalization = class_exists('\Arcanedev\Localization\Localization');
        $this->isMcamaraLocalization = class_exists('\Mcamara\LaravelLocalization\LaravelLocalization');
    }

    public function hasLocalization(): bool
    {
        return $this->isArcanedevLocalization || $this->isMcamaraLocalization;
    }

    public function isMultiLanguage(): bool
    {
        return $this->isMultiLanguage && $this->hasLocalization();
    }

    public function setLocale(?string $locale = null): ?string
    {
        if (! $this->isMultiLanguage) {
            return null;
        }

        if ($this->isArcanedevLocalization) {
            // @phpstan-ignore missingType.iterableValue
            return localization()->setLocale($locale);
        }

        if ($this->isMcamaraLocalization) {
            // @phpstan-ignore missingType.iterableValue
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
            return localization()->getSupportedLocales()->keys()->toArray();
        }

        if ($this->isMcamaraLocalization) {
            return \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales();
        }

        return [];
    }

    public function getSupportedLocalesInfo(): array
    {
        return collect($this->getSupportedLocales())->mapWithKeys(fn ($locale) => [
            $locale => [
                ...config('lazy.localization.locales.'.$locale),
                'url' => $this->getLocalizedURL($locale),
            ],
        ])->toArray();
    }

    public function setRouteLocale(string $prefix = '', ?string $locale = null): string
    {
        $locale = $this->setLocale($locale);

        if ($locale) {
            return $locale.'/'.$prefix;
        }

        return $prefix;
    }

    public function getLocalizedURL(string $code): string
    {
        if ($this->isArcanedevLocalization) {
            return localization()->getLocalizedURL($code);
        }

        if ($this->isMcamaraLocalization) {
            return \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($code);
        }

        return $code;
    }
}
