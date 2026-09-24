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
                            @if ($editRoute)
                                <a class="btn btn-ghost btn-sm" href="{{ route($editRoute, $user) }}">{{ __('Edit') }}</a>
                            @endif
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
