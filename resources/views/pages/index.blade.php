<x-lazy-layout>
    <div class="mx-auto max-w-7xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">{{ __('Pages') }}</h1>
                <p class="text-sm opacity-70">{{ __('Manage CMS pages, translations, publishing and hierarchy.') }}</p>
            </div>

            @if (!config('lazy.admin.permissions.enforce', true) || auth(config('lazy.auth.guard', 'web'))->user()?->can('pages.create'))
                <x-lazy-btn
                    primary
                    :href="route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.page.create')"
                    :label="__('Create page')"
                />
            @endif
        </div>

        @if (session('status'))
            <x-lazy-alert success :message="session('status')" />
        @endif

        <form method="GET" class="grid gap-3 md:grid-cols-4">
            <x-lazy-input type="search" name="search" :value="request('search')" :placeholder="__('Search pages')" />

            <x-lazy-select name="status" :placeholder="__('All statuses')">
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>
                        {{ ucfirst($status->value) }}
                    </option>
                @endforeach
            </x-lazy-select>

            <label class="flex items-center gap-2">
                <x-lazy-checkbox name="trashed" value="1" :checked="request()->boolean('trashed')" />
                <span>{{ __('Trash') }}</span>
            </label>

            <x-lazy-btn outline type="submit" :label="__('Filter')" />
        </form>

        <div class="overflow-x-auto rounded-2xl border border-base-300 bg-base-100 shadow-sm">
            <x-lazy-table class="w-full">
                <thead>
                    <tr>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Slug') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Updated') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pages as $page)
                        <tr>
                            <td>
                                <div class="font-medium">{{ $page->title ?: __('Untitled') }}</div>
                                @if ($page->key)
                                    <div class="text-xs opacity-60">{{ $page->key }}</div>
                                @endif
                            </td>
                            <td><code>{{ $page->path() }}</code></td>
                            <td><x-lazy-badge outline :label="$page->status->value" /></td>
                            <td>{{ $page->updated_at?->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    @if ($page->trashed())
                                        @if (!config('lazy.admin.permissions.enforce', true) || auth(config('lazy.auth.guard', 'web'))->user()?->can('pages.restore'))
                                            <form method="POST" action="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.page.restore', $page->id) }}">
                                                @csrf
                                                <x-lazy-btn sm outline type="submit" :label="__('Restore')" />
                                            </form>
                                        @endif
                                    @else
                                        @if (!config('lazy.admin.permissions.enforce', true) || auth(config('lazy.auth.guard', 'web'))->user()?->can('pages.preview'))
                                            <x-lazy-btn
                                                sm
                                                ghost
                                                :href="route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.page.preview', $page)"
                                                :label="__('Preview')"
                                            />
                                        @endif
                                        @if (!config('lazy.admin.permissions.enforce', true) || auth(config('lazy.auth.guard', 'web'))->user()?->can('pages.edit'))
                                            <x-lazy-btn
                                                sm
                                                outline
                                                :href="route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.page.edit', $page)"
                                                :label="__('Edit')"
                                            />
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center opacity-60">{{ __('No pages found.') }}</td></tr>
                    @endforelse
                </tbody>
            </x-lazy-table>
        </div>

        {{ $pages->links() }}
    </div>
</x-lazy-layout>
