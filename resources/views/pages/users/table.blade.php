<div class="flex flex-col gap-4">
    <div class="flex items-center justify-between gap-4">
        <div>
            @if ($createRoute)
                <a class="btn btn-primary" href="{{ route($createRoute) }}">{{ __('Create user') }}</a>
            @endif
        </div>
    </div>

    <div class="flex flex-wrap items-end gap-4">
        <label class="flex flex-col gap-1">
            <span>{{ __('Search') }}</span>
            <input type="search" wire:model.live.debounce.300ms="search" class="input input-bordered" placeholder="{{ __('Name or email') }}" />
        </label>
        <label class="flex flex-col gap-1">
            <span>{{ __('Created from') }}</span>
            <input type="date" wire:model.live="createdFrom" class="input input-bordered" />
        </label>
        <label class="flex flex-col gap-1">
            <span>{{ __('Created until') }}</span>
            <input type="date" wire:model.live="createdUntil" class="input input-bordered" />
        </label>
        <label class="flex flex-col gap-1">
            <span>{{ __('Per page') }}</span>
            <select wire:model.live="perPage" class="select select-bordered">
                @foreach ([10, 20, 30, 50, 100] as $size)
                    <option value="{{ $size }}">{{ $size }}</option>
                @endforeach
            </select>
        </label>
        <button type="button" wire:click="resetFilters" class="btn btn-ghost">{{ __('Reset filters') }}</button>
    </div>
    <div wire:loading role="status">{{ __('Loading…') }}</div>
    <div class="overflow-x-auto" wire:loading.class="opacity-50">
        <table class="table w-full">
            <thead>
                <tr>
                    @foreach (['id' => '#', 'name' => __('Nickname'), 'full_name' => __('Full name'), 'email' => __('Email'), 'photo' => __('Photo'), 'created_at' => __('Created at')] as $field => $label)
                        <th @if(in_array($field, ['id', 'name', 'email', 'created_at'], true)) aria-sort="{{ $sortField === $field ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}" @endif>
                            @if (in_array($field, ['id', 'name', 'email', 'created_at'], true))
                                <button type="button" wire:click="sortBy('{{ $field }}')">
                                    {{ $label }} @if($sortField === $field)<span aria-hidden="true">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                                </button>
                            @else
                                {{ $label }}
                            @endif
                        </th>
                    @endforeach
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr wire:key="user-{{ $user->getKey() }}">
                        <td>{{ $user->getKey() }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->full_name ?? '—' }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if ($avatar = $user->avatar ?? $user->profile_photo_url ?? null)
                                <img src="{{ $avatar }}" alt="{{ $user->name }}" width="96" height="96" loading="lazy" class="h-24 w-24 rounded-full object-cover" />
                            @else
                                <span>—</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at?->format('d/m/Y H:i:s') ?? '—' }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                @if ($showRoute)
                                    <a
                                        class="btn btn-secondary btn-sm btn-square"
                                        href="{{ route($showRoute, $user) }}"
                                        title="{{ __('View') }}"
                                        aria-label="{{ __('View') }}"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </a>
                                @endif

                                @if ($editRoute)
                                    <a
                                        class="btn btn-warning btn-sm btn-square"
                                        href="{{ route($editRoute, $user) }}"
                                        title="{{ __('Edit') }}"
                                        aria-label="{{ __('Edit') }}"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </a>
                                @endif

                                @if ($destroyRoute)
                                    <form method="POST" action="{{ route($destroyRoute, $user) }}"
                                          onsubmit="return confirm('{{ __('Delete this user?') }}')">
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-error btn-sm btn-square"
                                            title="{{ __('Delete') }}"
                                            aria-label="{{ __('Delete') }}"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">{{ __('No users found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p>{{ __('Showing :from–:to of :total users', ['from' => $users->firstItem() ?? 0, 'to' => $users->lastItem() ?? 0, 'total' => $users->total()]) }}</p>
    {{ $users->links() }}
</div>
