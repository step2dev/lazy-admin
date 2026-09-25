<?php

namespace Step2dev\LazyAdmin\Notifications;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Stringable;

class NotificationCenter
{
    public function availableFor(?object $user): bool
    {
        return $user !== null
            && method_exists($user, 'notifications')
            && Schema::hasTable('notifications');
    }

    public function unreadCount(?object $user): int
    {
        return $this->availableFor($user) ? $user->unreadNotifications()->count() : 0;
    }

    public function unread(?object $user, int $limit = 5): Collection
    {
        if (! $this->availableFor($user)) {
            return collect();
        }

        return $user->unreadNotifications()->latest()->limit($limit)->get();
    }

    public function latest(?object $user, int $limit = 50): Collection
    {
        if (! $this->availableFor($user)) {
            return collect();
        }

        return $user->notifications()->latest()->limit($limit)->get();
    }

    public function markRead(?object $user, string $id): ?string
    {
        if (! $this->availableFor($user)) {
            return null;
        }

        $notification = $user->notifications()->whereKey($id)->firstOrFail();
        $notification->markAsRead();

        return $this->url($notification);
    }

    public function markAllRead(?object $user): bool
    {
        if (! $this->availableFor($user)) {
            return false;
        }

        $user->unreadNotifications()->update(['read_at' => now()]);

        return true;
    }

    public function title(object $notification): string
    {
        if ($title = $this->localizedString(data_get($notification, 'data.title'))) {
            return $title;
        }

        if ($subject = $this->localizedString(data_get($notification, 'data.subject'))) {
            return $subject;
        }

        if ($type = $this->localizedString(data_get($notification, 'data.type'))) {
            return Str::headline($type);
        }

        if ($type = $this->localizedString(data_get($notification, 'type'))) {
            return Str::headline(class_basename($type));
        }

        $fallback = $this->localizedString(__('Notification'));

        return $fallback ?? 'Notification';
    }

    public function message(object $notification): ?string
    {
        return $this->localizedString(data_get($notification, 'data.message'))
            ?? $this->localizedString(data_get($notification, 'data.content'));
    }

    public function url(object $notification): ?string
    {
        return $this->localizedString(data_get($notification, 'data.url'));
    }

    private function localizedString(mixed $value): ?string
    {
        if (is_string($value)) {
            $value = trim($value);

            return $value !== '' ? $value : null;
        }

        if ($value instanceof Stringable) {
            $value = trim((string) $value);

            return $value !== '' ? $value : null;
        }

        if (! is_array($value)) {
            return null;
        }

        $locales = array_values(array_unique(array_filter([
            app()->getLocale(),
            config('app.fallback_locale'),
            'en',
        ], 'is_string')));

        foreach ($locales as $locale) {
            $candidate = $value[$locale] ?? null;

            if (is_string($candidate) && trim($candidate) !== '') {
                return trim($candidate);
            }
        }

        foreach ($value as $candidate) {
            if (is_string($candidate) && trim($candidate) !== '') {
                return trim($candidate);
            }
        }

        return null;
    }
}
