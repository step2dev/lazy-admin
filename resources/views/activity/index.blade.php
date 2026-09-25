<div>
    @php
        $displayValue = static function (mixed $value, string $fallback = '—'): string {
            if ($value === null || $value === '') {
                return $fallback;
            }

            if (is_string($value) || is_int($value) || is_float($value)) {
                return (string) $value;
            }

            if (is_bool($value)) {
                return $value ? 'true' : 'false';
            }

            if ($value instanceof \Stringable) {
                return (string) $value;
            }

            $encoded = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            return is_string($encoded) && $encoded !== '' ? $encoded : $fallback;
        };
    @endphp

    <div class="mb-6 grid grid-cols-1 gap-3 md:grid-cols-2">
        <x-lazy-input
            type="search"
            wire:model.live.debounce.300ms="search"
            :placeholder="__('Search activity')"
            aria-label="{{ __('Search activity') }}"
        />
        <x-lazy-input
            type="text"
            wire:model.live.debounce.300ms="event"
            :placeholder="__('Event')"
            aria-label="{{ __('Event') }}"
        />
    </div>

    <div wire:loading wire:target="search,event" class="mb-3">
        <x-lazy-loading sm />
    </div>

    @if($activities->isEmpty())
        <x-lazy-empty-state
            :title="__('No activity recorded yet.')"
            :description="__('Run the Spatie activity log migration if this is a new installation.')"
        />
    @else
        <x-lazy-table>
            <thead>
                <tr>
                    <th>{{ __('When') }}</th>
                    <th>{{ __('User') }}</th>
                    <th>{{ __('Event') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Changes') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activities as $activity)
                    @php
                        $causer = data_get($activity->causer, 'name');

                        if ($causer === null || $causer === '') {
                            $causer = data_get($activity->causer, 'email');
                        }

                        $ip = $activity->properties->get('ip');
                    @endphp

                    <tr wire:key="activity-{{ $activity->id }}">
                        <td class="whitespace-nowrap">{{ $activity->created_at?->format('Y-m-d H:i') }}</td>
                        <td>
                            <div>{{ $displayValue($causer) }}</div>
                            @if($ip !== null && $ip !== '')
                                <div class="mt-1 text-xs opacity-50">{{ $displayValue($ip) }}</div>
                            @endif
                        </td>
                        <td>
                            <x-lazy-badge ghost :label="$displayValue($activity->event)" />
                        </td>
                        <td>{{ $displayValue($activity->description) }}</td>
                        <td class="max-w-lg">
                            @if($activity->properties->has('old') || $activity->properties->has('new'))
                                <details>
                                    <summary class="cursor-pointer text-sm">{{ __('View changes') }}</summary>
                                    <pre class="mt-2 max-h-72 overflow-auto rounded-lg bg-base-200 p-3 text-xs">{{ json_encode([
                                        'old' => $activity->properties->get('old', []),
                                        'new' => $activity->properties->get('new', []),
                                        'ip' => $activity->properties->get('ip'),
                                        'user_agent' => $activity->properties->get('user_agent'),
                                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                                </details>
                            @else
                                <span class="opacity-50">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-lazy-table>

        <div class="mt-4">{{ $activities->links() }}</div>
    @endif
</div>
