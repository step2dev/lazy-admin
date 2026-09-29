<div
    class="relative hidden md:block"
    x-data="{
        open: false,
        focusSearch() {
            this.open = true;
            this.$nextTick(() => this.$refs.search?.focus());
        }
    }"
    @click.outside="open = false"
    @keydown.meta.k.window.prevent="focusSearch()"
    @keydown.ctrl.k.window.prevent="focusSearch()"
    @keydown.escape.window="open = false; $refs.search?.blur()"
>
    <div class="relative">
        <svg
            class="pointer-events-none absolute left-3 top-1/2 z-10 h-4 w-4 -translate-y-1/2 opacity-50"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            aria-hidden="true"
        >
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.35-4.35"></path>
        </svg>

        <x-lazy-input
            x-ref="search"
            type="search"
            wire:model.live.debounce.250ms="query"
            @focus="open = true"
            :placeholder="__('lazy-admin::search.short_placeholder')"
            aria-label="{{ __('lazy-admin::search.short_placeholder') }}"
            class="w-72 pl-9 pr-16 lg:w-80"
            minlength="2"
            maxlength="100"
            autocomplete="off"
        />

        <div wire:loading wire:target="query" class="absolute right-3 top-1/2 -translate-y-1/2">
            <x-lazy-loading xs />
        </div>

        <div
            wire:loading.remove
            wire:target="query"
            class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2"
            aria-hidden="true"
        >
            <x-lazy-kbd class="text-[0.65rem] opacity-60">⌘K</x-lazy-kbd>
        </div>
    </div>

    @if(mb_strlen(trim($query)) >= 2)
        <div
            x-show="open"
            x-cloak
            class="absolute right-0 z-50 mt-2 w-[26rem] max-w-[calc(100vw-2rem)]"
        >
            <x-lazy-card compact class="border border-base-300 bg-base-100 shadow-xl">
                <div class="mb-2 flex items-center justify-between gap-3 px-1">
                    <span class="text-xs font-semibold uppercase tracking-wider opacity-50">
                        {{ __('Search results') }}
                    </span>
                    <span class="text-xs opacity-50">{{ count($results) }}</span>
                </div>

                @forelse(collect($results)->groupBy('provider') as $provider => $providerResults)
                    <div class="mb-2 last:mb-0">
                        <div class="px-2 py-1 text-xs font-semibold uppercase tracking-wider opacity-40">
                            {{ str($provider)->headline() }}
                        </div>

                        <div class="space-y-1">
                            @foreach($providerResults as $result)
                                <x-lazy-btn
                                    :href="$result['url']"
                                    ghost
                                    class="h-auto min-h-0 w-full justify-start px-3 py-2 text-left"
                                >
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate font-medium">{{ $result['title'] }}</span>
                                        <span class="mt-0.5 block truncate text-xs opacity-60">
                                            {{ $result['description'] ?: ($result['type'] ?: $result['provider']) }}
                                        </span>
                                    </span>
                                </x-lazy-btn>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <x-lazy-empty-state
                        :title="__('lazy-admin::search.no_results')"
                        class="border-0 p-3 shadow-none"
                    />
                @endforelse

                <div class="mt-2 border-t border-base-300 pt-2">
                    <x-lazy-btn
                        :href="route(trim((string) config('lazy.admin.route.name', 'admin.'), '.').'.search', ['q' => $query])"
                        ghost
                        sm
                        block
                        :label="__('lazy-admin::search.view_all')"
                    />
                </div>
            </x-lazy-card>
        </div>
    @endif
</div>
