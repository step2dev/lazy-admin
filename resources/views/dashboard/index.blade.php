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
            @php
                $customWidgets = $items->where('type', 'custom')->values();
                $metricWidgets = $items->where('type', 'metric')->values();
            @endphp

            <section class="space-y-3">
                <div class="flex items-center justify-between gap-3 px-1">
                    <h2 class="text-sm font-semibold uppercase tracking-wide opacity-60">
                        {{ $section }}
                    </h2>

                    <span class="text-xs tabular-nums opacity-35">
                        {{ $items->count() }}
                    </span>
                </div>

                @if($customWidgets->isNotEmpty())
                    <div class="grid grid-cols-12 gap-3">
                        @foreach($customWidgets as $widget)
                            <div
                                @class([
                                    'col-span-12',
                                    'md:col-span-1' => $widget['span'] === 1,
                                    'md:col-span-2' => $widget['span'] === 2,
                                    'md:col-span-3' => $widget['span'] === 3,
                                    'md:col-span-4' => $widget['span'] === 4,
                                    'md:col-span-5' => $widget['span'] === 5,
                                    'md:col-span-6' => $widget['span'] === 6,
                                    'md:col-span-7' => $widget['span'] === 7,
                                    'md:col-span-8' => $widget['span'] === 8,
                                    'md:col-span-9' => $widget['span'] === 9,
                                    'md:col-span-10' => $widget['span'] === 10,
                                    'md:col-span-11' => $widget['span'] === 11,
                                    'md:col-span-12' => $widget['span'] >= 12,
                                ])
                                wire:key="dashboard-custom-{{ $widget['id'] }}"
                            >
                                @include($widget['view'], $widget['data'])
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($metricWidgets->isNotEmpty())
                    @php($progressSection = $metricWidgets->every(fn (array $widget) => $widget['progress'] !== null))

                    @if($progressSection)
                        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                            @foreach($metricWidgets as $widget)
                                @php($tone = $widget['tone'] ?? 'neutral')

                                <a
                                    @if($widget['url']) href="{{ $widget['url'] }}" @endif
                                    class="card border border-base-300 bg-base-200 shadow-xs transition hover:bg-base-300/40"
                                >
                                    <div class="card-body items-center gap-2 p-4 text-center">
                                        <div
                                            @class([
                                                'radial-progress',
                                                'text-base-content/60' => $tone === 'neutral',
                                                'text-info' => $tone === 'info',
                                                'text-success' => $tone === 'success',
                                                'text-warning' => $tone === 'warning',
                                                'text-error' => $tone === 'error',
                                            ])
                                            style="--value: {{ $widget['progress'] }};"
                                            role="progressbar"
                                            aria-valuenow="{{ $widget['progress'] }}"
                                            aria-valuemin="0"
                                            aria-valuemax="100"
                                        >
                                            {{ number_format($widget['progress'], 0) }}%
                                        </div>

                                        <div class="font-semibold">{{ $widget['label'] }}</div>
                                        <div class="text-2xl font-semibold tabular-nums">{{ $widget['value'] }}</div>

                                        @if($widget['description'])
                                            <div class="line-clamp-2 text-xs opacity-55">{{ $widget['description'] }}</div>
                                        @endif

                                        <progress
                                            @class([
                                                'progress mt-1 w-full',
                                                'progress-info' => $tone === 'info',
                                                'progress-success' => $tone === 'success',
                                                'progress-warning' => $tone === 'warning',
                                                'progress-error' => $tone === 'error',
                                            ])
                                            value="{{ $widget['progress'] }}"
                                            max="100"
                                        ></progress>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-box border border-base-300 bg-base-200 shadow-xs">
                            <div class="stats stats-vertical min-w-full bg-transparent lg:stats-horizontal">
                                @foreach($metricWidgets as $widget)
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
                    @endif
                @endif
            </section>
        @endforeach
    @endif
</div>
