<?php

namespace Step2dev\LazyAdmin\Integrations;

use Illuminate\Database\Eloquent\Model;

final class SeoRedirectsIntegration
{
    public const MODEL = 'Step2dev\\LazySeoRedirect\\Models\\SeoRedirect';

    public static function available(): bool
    {
        return self::modelClass() !== null;
    }

    /**
     * @return class-string<Model>|null
     */
    public static function modelClass(): ?string
    {
        if (! (bool) config('lazy.admin.integrations.seo_redirects.enabled', true)) {
            return null;
        }

        if (! class_exists(self::MODEL) || ! is_subclass_of(self::MODEL, Model::class)) {
            return null;
        }

        return self::MODEL;
    }

    /**
     * @return list<string>
     */
    public static function permissions(): array
    {
        return [
            'seo_redirects.view',
            'seo_redirects.create',
            'seo_redirects.edit',
            'seo_redirects.delete',
        ];
    }
}
