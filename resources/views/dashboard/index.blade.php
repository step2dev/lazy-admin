<div class="space-y-6">
    @if($widgets === [])
        <x-lazy-empty-state
            :title="__('No dashboard widgets yet')"
            :description="__('Installed modules can register widgets with the Lazy Admin dashboard registry.')"
        />
    @else
        @php
            $sections = collect($widgets)->groupBy(fn (array $widget) => $widget['group'] ?: __('Overview'));
        @endphp

        @foreach($sections as $section => $items)
            <section class="space-y-3">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold uppercase tracking-wide opacity-65">
                            {{ $section }}
                        </h2>
                    </div>
                    <span class="text-xs opacity-40">{{ $items->count() }}</span>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-5">
                    @foreach($items as $widget)
                        @php($tone = $widget['tone'] ?? 'neutral')

                        <x-lazy-card
                            :href="$widget['url']"
                            :hover="(bool) $widget['url']"
                            @class([
                                'group min-h-28 border bg-base-100 shadow-sm transition',
                                'border-base-300' => $tone === 'neutral',
                                'border-info/30 bg-info/5' => $tone === 'info',
                                'border-success/30 bg-success/5' => $tone === 'success',
                                'border-warning/30 bg-warning/5' => $tone === 'warning',
                                'border-error/30 bg-error/5' => $tone === 'error',
                            ])
                        >
                            <div class="flex items-start justify-between gap-3">
                                <span class="text-sm font-medium opacity-65">{{ $widget['label'] }}</span>

                                @if($widget['url'])
                                    <span
                                        @class([
                                            'flex size-8 shrink-0 items-center justify-center rounded-full transition',
                                            'bg-base-200 group-hover:bg-primary group-hover:text-primary-content' => $tone === 'neutral',
                                            'bg-info/15 text-info' => $tone === 'info',
                                            'bg-success/15 text-success' => $tone === 'success',
                                            'bg-warning/15 text-warning' => $tone === 'warning',
                                            'bg-error/15 text-error' => $tone === 'error',
                                        ])
                                        aria-hidden="true"
                                    >
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M5 12h14"></path>
                                            <path d="m13 6 6 6-6 6"></path>
                                        </svg>
                                    </span>
                                @endif
                            </div>

                            <strong
                                @class([
                                    'mt-auto break-words text-3xl font-semibold tracking-tight',
                                    'text-info' => $tone === 'info',
                                    'text-success' => $tone === 'success',
                                    'text-warning' => $tone === 'warning',
                                    'text-error' => $tone === 'error',
                                ])
                            >
                                {{ $widget['value'] }}
                            </strong>

                            @if($widget['description'])
                                <span class="text-xs leading-5 opacity-55">{{ $widget['description'] }}</span>
                            @endif
                        </x-lazy-card>
                    @endforeach
                </div>
            </section>
        @endforeach
    @endif
</div>
