<?php

namespace Step2dev\LazyAdmin\Localization\Contracts;

interface LocalizationInterface
{
    public function setLocale(?string $locale = null): ?string;

    public function getLocale(): string;

    public function getSupportedLocales(): array;

    public function setRouteLocale(string $prefix = '', ?string $locale = null): string;

    public function getLocalizedURL(string|null $code,string|null $url = null): string;

    public function localizeURL(string|null $url = null, string|null $locale = null): string;
}
