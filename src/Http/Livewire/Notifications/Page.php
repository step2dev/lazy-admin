<?php

namespace Step2dev\LazyAdmin\Http\Livewire\Notifications;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Step2dev\LazyAdmin\Notifications\NotificationCenter;

#[Layout('lazy::livewire-layout', ['title' => 'Notifications'])]
class Page extends Component
{
    public function markRead(string $notification): mixed
    {
        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();
        $center = app(NotificationCenter::class);

        abort_unless($user !== null && $center->availableFor($user), 404);

        $url = $center->markRead($user, $notification);

        return $url !== null ? redirect()->to($url) : null;
    }

    public function markAllRead(): void
    {
        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();

        abort_unless(app(NotificationCenter::class)->markAllRead($user), 404);
    }

    public function render(): View
    {
        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();

        abort_unless($user !== null, 403);

        $center = app(NotificationCenter::class);

        return lazyView('lazy::notifications.index', [
            'notifications' => $center->latest($user),
            'notificationCenter' => $center,
        ]);
    }
}
