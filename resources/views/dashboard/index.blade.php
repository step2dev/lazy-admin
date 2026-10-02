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
            <section class="space-y-2.5">
                <div class="flex items-center justify-between gap-3 px-1">
                    <h2 class="text-sm font-semibold uppercase tracking-wide opacity-60">
                        {{ $section }}
                    </h2>

                    <span class="text-xs tabular-nums opacity-35">
                        {{ $items->count() }}
                    </span>
                </div>

                <div class="overflow-x-auto rounded-box border border-base-300 bg-base-200 shadow-xs">
                    <div class="stats stats-vertical min-w-full bg-transparent lg:stats-horizontal">
                        @foreach($items as $widget)
                            @php($tone = $widget['tone'] ?? 'neutral')

                            <a
                                @if($widget['url']) href="{{ $widget['url'] }}" @endif
                                @class([
                                    'stat group min-w-56 transition',
                                    'cursor-pointer hover:bg-base-300/40 focus:outline-none focus-visible:bg-base-300/40' => $widget['url'],
                                ])
                            >
                                <div
                                    @class([
                                        'stat-figure flex size-9 items-center justify-center rounded-full',
                                        'bg-base-300/70 text-base-content/55' => $tone === 'neutral',
                                        'bg-info/15 text-info' => $tone === 'info',
                                        'bg-success/15 text-success' => $tone === 'success',
                                        'bg-warning/15 text-warning' => $tone === 'warning',
                                        'bg-error/15 text-error' => $tone === 'error',
                                    ])
                                >
                                    @if($widget['url'])
                                        <svg
                                            class="size-4 transition-transform group-hover:translate-x-0.5"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            aria-hidden="true"
                                        >
                                            <path d="M5 12h14"></path>
                                            <path d="m13 6 6 6-6 6"></path>
                                        </svg>
                                    @else
                                        <span class="status status-sm bg-current"></span>
                                    @endif
                                </div>

                                <div class="stat-title text-xs font-medium">
                                    {{ $widget['label'] }}
                                </div>

                                <div
                                    @class([
                                        'stat-value text-xl font-semibold tabular-nums',
                                        'text-info' => $tone === 'info',
                                        'text-success' => $tone === 'success',
                                        'text-warning' => $tone === 'warning',
                                        'text-error' => $tone === 'error',
                                    ])
                                >
                                    {{ $widget['value'] }}
                                </div>

                                @if($widget['description'])
                                    <div class="stat-desc max-w-56 truncate" title="{{ $widget['description'] }}">
                                        {{ $widget['description'] }}
                                    </div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endforeach
    @endif
</div>
