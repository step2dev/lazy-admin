<div>
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
                    <tr wire:key="activity-{{ $activity['id'] }}">
                        <td class="whitespace-nowrap">{{ $activity['when'] }}</td>
                        <td>
                            <div>{{ $activity['user'] }}</div>
                            @if($activity['ip'] !== '')
                                <div class="mt-1 text-xs opacity-50">{{ $activity['ip'] }}</div>
                            @endif
                        </td>
                        <td>
                            <x-lazy-badge ghost :label="$activity['event']" />
                        </td>
                        <td>{{ $activity['description'] }}</td>
                        <td class="max-w-lg">
                            @if($activity['has_changes'])
                                <details>
                                    <summary class="cursor-pointer text-sm">{{ __('View changes') }}</summary>
                                    <pre class="mt-2 max-h-72 overflow-auto rounded-lg bg-base-200 p-3 text-xs">{{ $activity['changes'] }}</pre>
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
