<div>
    <div class="mb-4 flex justify-end">
        <x-lazy-btn
            wire:click="markAllRead"
            ghost
            sm
            :label="__('Mark all as read')"
        />
    </div>

    @if($notifications->isEmpty())
        <x-lazy-empty-state :title="__('No notifications yet.')" />
    @else
        <x-lazy-card class="flex flex-col gap-1 p-2">
            @foreach($notifications as $notification)
                <x-lazy-btn
                    wire:click="markRead(@js($notification->id))"
                    ghost
                    block
                    class="h-auto justify-between px-4 py-3 text-left"
                >
                    <span>
                        <span class="block font-medium">{{ $notificationCenter->title($notification) }}</span>
                        @if($notificationCenter->message($notification))
                            <span class="mt-1 block text-sm opacity-70">{{ $notificationCenter->message($notification) }}</span>
                        @endif
                    </span>
                    <span class="flex shrink-0 items-center gap-2">
                        @if(is_null($notification->read_at))
                            <x-lazy-badge primary xs :label="__('New')" />
                        @endif
                        <span class="text-xs opacity-60">{{ $notification->created_at?->diffForHumans() }}</span>
                    </span>
                </x-lazy-btn>
            @endforeach
        </x-lazy-card>
    @endif
</div>
