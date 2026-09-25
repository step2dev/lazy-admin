<?php

namespace Step2dev\LazyAdmin\Notifications;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

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
        $title = data_get($notification, 'data.title');

        if (is_string($title) && $title !== '') {
            return $title;
        }

        $fallback = __('Notification');

        return is_string($fallback) && $fallback !== '' ? $fallback : 'Notification';
    }

    public function message(object $notification): ?string
    {
        $message = data_get($notification, 'data.message');

        return is_string($message) && $message !== '' ? $message : null;
    }

    public function url(object $notification): ?string
    {
        $url = data_get($notification, 'data.url');

        return is_string($url) && $url !== '' ? $url : null;
    }
}
