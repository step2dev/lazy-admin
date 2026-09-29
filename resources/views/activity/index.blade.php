<div class="space-y-4">
    <x-lazy-card compact class="border border-base-300 bg-base-100 shadow-sm">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5">
            <x-lazy-input
                type="search"
                wire:model.live.debounce.300ms="search"
                :placeholder="__('lazy-admin::activity.search')"
                aria-label="{{ __('lazy-admin::activity.search') }}"
            />

            <x-lazy-input
                type="text"
                wire:model.live.debounce.300ms="event"
                :placeholder="__('lazy-admin::activity.event')"
                aria-label="{{ __('lazy-admin::activity.event') }}"
            />

            <x-lazy-input
                type="date"
                wire:model.live="dateFrom"
                aria-label="{{ __('lazy-admin::activity.date_from') }}"
            />

            <x-lazy-input
                type="date"
                wire:model.live="dateUntil"
                aria-label="{{ __('lazy-admin::activity.date_until') }}"
            />

            <x-lazy-btn
                type="button"
                wire:click="resetFilters"
                ghost
                :label="__('lazy-admin::activity.reset')"
            />
        </div>
    </x-lazy-card>

    <div wire:loading wire:target="search,event,dateFrom,dateUntil" class="flex items-center gap-2 text-sm opacity-60">
        <x-lazy-loading xs />
        <span>{{ __('Loading…') }}</span>
    </div>

    @if($activities->isEmpty())
        <x-lazy-empty-state
            :title="__('lazy-admin::activity.empty.title')"
            :description="__('lazy-admin::activity.empty.description')"
        />
    @else
        <div class="overflow-hidden rounded-2xl border border-base-300 bg-base-100 shadow-sm">
            <div class="overflow-x-auto">
                <x-lazy-table class="w-full">
                    <thead>
                        <tr>
                            <th>{{ __('lazy-admin::activity.columns.when') }}</th>
                            <th>{{ __('lazy-admin::activity.columns.user') }}</th>
                            <th>{{ __('lazy-admin::activity.columns.event') }}</th>
                            <th>{{ __('lazy-admin::activity.columns.description') }}</th>
                            <th>{{ __('lazy-admin::activity.columns.changes') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activities as $activity)
                            <tr wire:key="activity-{{ $activity['id'] }}" class="hover:bg-base-200/50">
                                <td class="whitespace-nowrap text-sm">{{ $activity['when'] }}</td>
                                <td>
                                    <div class="font-medium">{{ $activity['user'] }}</div>
                                    @if($activity['ip'] !== '')
                                        <div class="mt-1 font-mono text-xs opacity-45">{{ $activity['ip'] }}</div>
                                    @endif
                                </td>
                                <td>
                                    <x-lazy-badge ghost :label="$activity['event']" />
                                </td>
                                <td class="max-w-xl">{{ $activity['description'] }}</td>
                                <td class="max-w-lg">
                                    @if($activity['has_changes'])
                                        <x-lazy-collapse
                                            class="min-w-72 border border-base-300 bg-base-200/40"
                                            summary-class="text-sm font-medium"
                                            content-class="pt-2"
                                        >
                                            <x-slot:summary>{{ __('lazy-admin::activity.view_changes') }}</x-slot:summary>
                                            <pre class="max-h-72 overflow-auto rounded-lg bg-base-200 p-3 text-xs">{{ $activity['changes'] }}</pre>
                                        </x-lazy-collapse>
                                    @else
                                        <span class="opacity-40">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-lazy-table>
            </div>

            <div class="border-t border-base-300 px-4 py-3">
                {{ $activities->links() }}
            </div>
        </div>
    @endif
</div>
