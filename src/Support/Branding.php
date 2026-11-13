<?php

namespace Step2dev\LazyAdmin\Support;

use Illuminate\Support\Facades\Storage;

class Branding
{
    public static function logoUrl(bool $dark = false, mixed $fallback = null): ?string
    {
        $logo = setting('admin.logo', $fallback);

        if ($dark) {
            $candidate = setting('admin.logo_dark');
            if (is_string($candidate) && $candidate !== '') {
                $logo = $candidate;
            }
        }

        if (! is_string($logo) || $logo === '') {
            return null;
        }

        if (str_starts_with($logo, '/') || str_starts_with($logo, 'https://')
            || str_starts_with($logo, 'http://') || str_starts_with($logo, 'data:')) {
            return $logo;
        }

        return Storage::disk('public')->url($logo);
    }
}
