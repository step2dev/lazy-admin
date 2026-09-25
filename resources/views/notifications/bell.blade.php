<div>
    @if($available)
        <x-lazy-dropdown>
            <x-slot:trigger>
                <x-lazy-btn ghost circle aria-label="{{ __('Notifications') }}">
                    <span class="indicator">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($unreadCount > 0)
                            <x-lazy-badge primary xs class="indicator-item">
                                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                            </x-lazy-badge>
                        @endif
                    </span>
                </x-lazy-btn>
            </x-slot:trigger>

            <div class="flex items-center justify-between px-2 py-2">
                <strong>{{ __('Notifications') }}</strong>
                @if($unreadCount > 0)
                    <x-lazy-btn
                        wire:click="markAllRead"
                        ghost
                        xs
                        :label="__('Mark all read')"
                    />
                @endif
            </div>

            @forelse($notifications as $notification)
                <x-lazy-btn
                    :wire:click="'markRead('.\Illuminate\Support\Js::from($notification->id)->toHtml().')'"
                    ghost
                    block
                    class="h-auto justify-start p-3 text-left"
                >
                    <span>
                        <span class="block text-sm font-medium">{{ $notificationCenter->title($notification) }}</span>
                        @if($notificationCenter->message($notification))
                            <span class="mt-1 block line-clamp-2 text-xs opacity-70">{{ $notificationCenter->message($notification) }}</span>
                        @endif
                        <span class="mt-1 block text-xs opacity-50">{{ $notification->created_at?->diffForHumans() }}</span>
                    </span>
                </x-lazy-btn>
            @empty
                <x-lazy-empty-state :title="__('No unread notifications')" class="border-0 p-3 shadow-none" />
            @endforelse

            <x-lazy-btn
                :href="route(trim((string) config('lazy.admin.route.name', 'admin.'), '.').'.notifications.index')"
                ghost
                sm
                block
                :label="__('View all notifications')"
                class="mt-1"
            />
        </x-lazy-dropdown>
    @endif
</div>
