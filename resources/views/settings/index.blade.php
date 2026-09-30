<div class="grid grid-cols-1 gap-6 lg:grid-cols-[18rem_minmax(0,1fr)]">
    <aside class="lg:sticky lg:top-24 lg:self-start">
        <x-lazy-card compact class="border border-base-300 bg-base-100 shadow-sm">
            <div class="mb-2 px-2">
                <div class="text-xs font-semibold uppercase tracking-wider opacity-50">
                    {{ __('lazy-admin::settings.title') }}
                </div>
            </div>

            <nav class="flex flex-col gap-1" aria-label="{{ __('lazy-admin::settings.sections') }}">
                @foreach($sections as $item)
                    @if($activeSection['id'] === $item['id'])
                        <x-lazy-btn
                            type="button"
                            wire:click="selectSection('{{ $item['id'] }}')"
                            primary
                            soft
                            class="h-auto w-full justify-start rounded-xl px-4 py-3 text-left"
                        >
                            <span class="min-w-0">
                                <span class="block truncate font-medium">{{ $item['label'] }}</span>
                                @if($item['description'])
                                    <span class="mt-1 block line-clamp-2 text-xs opacity-70">{{ $item['description'] }}</span>
                                @endif
                            </span>
                        </x-lazy-btn>
                    @else
                        <x-lazy-btn
                            type="button"
                            wire:click="selectSection('{{ $item['id'] }}')"
                            ghost
                            class="h-auto w-full justify-start rounded-xl px-4 py-3 text-left"
                        >
                            <span class="min-w-0">
                                <span class="block truncate font-medium">{{ $item['label'] }}</span>
                                @if($item['description'])
                                    <span class="mt-1 block line-clamp-2 text-xs opacity-60">{{ $item['description'] }}</span>
                                @endif
                            </span>
                        </x-lazy-btn>
                    @endif
                @endforeach
            </nav>
        </x-lazy-card>
    </aside>

    <section class="min-w-0 rounded-2xl border border-base-300 bg-base-100 p-4 shadow-sm sm:p-6">
        <div class="mb-6 border-b border-base-300 pb-4">
            <h2 class="text-xl font-semibold">{{ $activeSection['label'] }}</h2>

            @if($activeSection['description'])
                <p class="mt-1 text-sm opacity-60">{{ $activeSection['description'] }}</p>
            @endif
        </div>

        @livewire($activeSection['component'], [], key('settings-'.$activeSection['id']))
    </section>
</div>
