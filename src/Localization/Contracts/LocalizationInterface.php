<?php

namespace Step2dev\LazyAdmin\Localization\Contracts;

interface LocalizationInterface
{
    public function setLocale(?string $locale = null): ?string;

    public function getLocale(): string;

    public function getSupportedLocales(): array;

    public function setRouteLocale(string $prefix = '', ?string $locale = null): string;
}
