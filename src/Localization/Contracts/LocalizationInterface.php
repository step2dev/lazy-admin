<?php

namespace Step2dev\LazyAdmin\Localization\Contracts;

interface LocalizationInterface
{
    public function setLocale(string|null $locale = null): string|null;

    public function getLocale(): string;

    public function getSupportedLocales(): array;

    public function setRouteLocale(string $prefix = '', string|null $locale = null): string;
}
