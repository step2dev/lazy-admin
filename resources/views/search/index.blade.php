<div class="space-y-4">
    <x-lazy-card compact class="border border-base-300 bg-base-100 shadow-sm">
        <div role="search">
            <x-lazy-input
                type="search"
                wire:model.live.debounce.250ms="query"
                minlength="2"
                maxlength="100"
                :placeholder="__('lazy-admin::search.placeholder')"
                aria-label="{{ __('lazy-admin::search.placeholder') }}"
                autofocus
            />

            <div wire:loading wire:target="query" class="mt-2 flex items-center gap-2 text-sm opacity-60">
                <x-lazy-loading xs />
                <span>{{ __('Loading…') }}</span>
            </div>
        </div>
    </x-lazy-card>

    @if(mb_strlen($query) < 2)
        <x-lazy-alert :message="__('lazy-admin::search.min_length')" />
    @elseif($results === [])
        <x-lazy-empty-state :title="__('lazy-admin::search.empty')" />
    @else
        <div class="space-y-4">
            @foreach(collect($results)->groupBy('provider') as $provider => $providerResults)
                <x-lazy-card compact class="border border-base-300 bg-base-100 shadow-sm">
                    <div class="mb-2 flex items-center justify-between gap-3 px-1">
                        <h2 class="text-sm font-semibold uppercase tracking-wider opacity-55">
                            {{ str($provider)->headline() }}
                        </h2>
                        <x-lazy-badge ghost xs :label="(string) $providerResults->count()" />
                    </div>

                    <div class="space-y-1">
                        @foreach($providerResults as $result)
                            <x-lazy-btn
                                :href="$result['url']"
                                ghost
                                class="h-auto w-full justify-between px-4 py-3 text-left"
                            >
                                <span class="min-w-0">
                                    <span class="block truncate font-medium">{{ $result['title'] }}</span>
                                    @if($result['description'])
                                        <span class="mt-1 block truncate text-sm opacity-65">{{ $result['description'] }}</span>
                                    @endif
                                </span>

                                <x-lazy-badge ghost xs class="shrink-0">
                                    {{ $result['type'] ?: $result['provider'] }}
                                </x-lazy-badge>
                            </x-lazy-btn>
                        @endforeach
                    </div>
                </x-lazy-card>
            @endforeach
        </div>
    @endif
</div>
