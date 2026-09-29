<div class="space-y-4">
    <div class="flex justify-end">
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
        <x-lazy-card compact class="border border-base-300 bg-base-100 shadow-sm">
            <div class="divide-y divide-base-300">
                @foreach($notifications as $notification)
                    <x-lazy-btn
                        :wire:click="'markRead('.IlluminateSupportJs::from($notification->id)->toHtml().')'"
                        ghost
                        block
                        class="h-auto rounded-none px-4 py-4 text-left first:rounded-t-xl last:rounded-b-xl"
                    >
                        <span class="flex min-w-0 flex-1 items-start gap-3">
                            <span class="mt-2 size-2 shrink-0 rounded-full {{ is_null($notification->read_at) ? 'bg-primary' : 'bg-base-300' }}"></span>

                            <span class="min-w-0 flex-1">
                                <span class="block font-medium">{{ $notificationCenter->title($notification) }}</span>
                                @if($notificationCenter->message($notification))
                                    <span class="mt-1 block text-sm leading-5 opacity-65">{{ $notificationCenter->message($notification) }}</span>
                                @endif
                                <span class="mt-2 block text-xs opacity-45">{{ $notification->created_at?->diffForHumans() }}</span>
                            </span>
                        </span>

                        @if(is_null($notification->read_at))
                            <x-lazy-badge primary xs :label="__('New')" />
                        @endif
                    </x-lazy-btn>
                @endforeach
            </div>
        </x-lazy-card>
    @endif
</div>
