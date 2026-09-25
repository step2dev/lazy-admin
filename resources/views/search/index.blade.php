<div>
    <div class="mb-6" role="search">
        <x-lazy-input
            type="search"
            wire:model.live.debounce.250ms="query"
            minlength="2"
            maxlength="100"
            :placeholder="__('Search admin content')"
            aria-label="{{ __('Search admin content') }}"
            autofocus
        />
        <div wire:loading wire:target="query" class="mt-2 text-sm opacity-60">
            <x-lazy-loading xs />
        </div>
    </div>

    @if(mb_strlen($query) < 2)
        <x-lazy-alert :message="__('Enter at least two characters.')" />
    @elseif($results === [])
        <x-lazy-empty-state :title="__('No results found.')" />
    @else
        <div class="flex flex-col gap-1 rounded-2xl border border-base-300 bg-base-100 p-2">
            @foreach($results as $result)
                <x-lazy-btn
                    :href="$result['url']"
                    ghost
                    class="h-auto w-full justify-between px-4 py-3 text-left"
                >
                    <span class="min-w-0">
                        <span class="block font-medium">{{ $result['title'] }}</span>
                        @if($result['description'])
                            <span class="mt-1 block truncate text-sm opacity-70">{{ $result['description'] }}</span>
                        @endif
                    </span>
                    <x-lazy-badge ghost xs class="shrink-0">
                        {{ $result['type'] ?: $result['provider'] }}
                    </x-lazy-badge>
                </x-lazy-btn>
            @endforeach
        </div>
    @endif
</div>
