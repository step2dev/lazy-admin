<?php

namespace Step2dev\LazyAdmin\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Step2dev\LazyAdmin\Notifications\NotificationCenter;

class NotificationController extends Controller
{
    public function __construct(private readonly NotificationCenter $notifications) {}

    public function index(): View
    {
        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();

        abort_unless($user !== null, 403);

        return lazyView('lazy::notifications.index', [
            'notifications' => $this->notifications->latest($user),
            'notificationCenter' => $this->notifications,
        ]);
    }

    public function markRead(string $notification): RedirectResponse
    {
        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();

        abort_unless($user !== null && $this->notifications->availableFor($user), 404);

        $url = $this->notifications->markRead($user, $notification);

        return $url !== null ? redirect()->to($url) : back();
    }

    public function markAllRead(): RedirectResponse
    {
        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();

        abort_unless($user !== null && $this->notifications->markAllRead($user), 404);

        return back()->with('status', __('All notifications marked as read.'));
    }
}
