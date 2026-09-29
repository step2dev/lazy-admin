<div>
    @if($widgets === [])
        <x-lazy-empty-state
            :title="__('No dashboard widgets yet')"
            :description="__('Installed modules can register widgets with the Lazy Admin dashboard registry.')"
        />
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach($widgets as $widget)
                <x-lazy-card
                    :href="$widget['url']"
                    :hover="(bool) $widget['url']"
                    class="group min-h-36 border border-base-300 bg-base-100 shadow-sm"
                >
                    <div class="flex items-start justify-between gap-4">
                        <span class="text-sm font-medium opacity-65">{{ $widget['label'] }}</span>

                        @if($widget['url'])
                            <span
                                class="flex size-8 shrink-0 items-center justify-center rounded-full bg-base-200 transition group-hover:bg-primary group-hover:text-primary-content"
                                aria-hidden="true"
                            >
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14"></path>
                                    <path d="m13 6 6 6-6 6"></path>
                                </svg>
                            </span>
                        @endif
                    </div>

                    <strong class="mt-auto break-words text-3xl font-semibold tracking-tight">
                        {{ $widget['value'] }}
                    </strong>

                    @if($widget['description'])
                        <span class="text-xs leading-5 opacity-55">{{ $widget['description'] }}</span>
                    @endif
                </x-lazy-card>
            @endforeach
        </div>
    @endif
</div>
