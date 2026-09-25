<div class="relative hidden md:block" x-data="{ open: false }" @click.outside="open = false">
    <x-lazy-input
        type="search"
        wire:model.live.debounce.250ms="query"
        @focus="open = true"
        :placeholder="__('lazy-admin::search.short_placeholder')"
        aria-label="{{ __('lazy-admin::search.short_placeholder') }}"
        class="w-72"
        minlength="2"
        maxlength="100"
        autocomplete="off"
    />

    <div wire:loading wire:target="query" class="absolute right-3 top-3">
        <x-lazy-loading xs />
    </div>

    @if(mb_strlen(trim($query)) >= 2)
        <div x-show="open" x-cloak class="absolute right-0 z-50 mt-2">
            <x-lazy-card class="w-96 p-2 shadow-xl">
                @forelse($results as $result)
                    <x-lazy-btn
                        :href="$result['url']"
                        ghost
                        class="h-auto w-full justify-start px-3 py-2 text-left"
                    >
                        <span class="min-w-0">
                            <span class="block font-medium">{{ $result['title'] }}</span>
                            <span class="block truncate text-xs opacity-60">
                                {{ $result['description'] ?: ($result['type'] ?: $result['provider']) }}
                            </span>
                        </span>
                    </x-lazy-btn>
                @empty
                    <x-lazy-empty-state :title="__('lazy-admin::search.no_results')" class="border-0 p-3 shadow-none" />
                @endforelse

                <x-lazy-btn
                    :href="route(trim((string) config('lazy.admin.route.name', 'admin.'), '.').'.search', ['q' => $query])"
                    ghost
                    sm
                    block
                    :label="__('lazy-admin::search.view_all')"
                    class="mt-1"
                />
            </x-lazy-card>
        </div>
    @endif
</div>
